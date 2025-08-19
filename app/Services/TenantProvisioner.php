<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantProvisioner
{
    /**
     * Create a new tenant database by cloning from a sample schema and return connection details.
     * NOTE: Requires the MySQL user to have CREATE DATABASE and privileges to read the sample DB.
     */
    public static function createDraftDatabase(string $baseName = 'client_site_'): array
    {
        $dbName = $baseName . Str::random(8);

        // Create database
        DB::statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Clone structure from sample database (assumes 'smartprep_sample-website')
        // For portability we copy table-by-table using SHOW TABLES
        $sampleDb = env('SAMPLE_WEBSITE_DB', 'smartprep_sample-website');
        $tables = DB::select("SELECT table_name as t FROM information_schema.tables WHERE table_schema = ?", [$sampleDb]);

        foreach ($tables as $row) {
            $table = $row->t;
            // Create table structure in new db
            $createStmtRow = DB::selectOne("SHOW CREATE TABLE `{$sampleDb}`.`{$table}`");
            $createSql = $createStmtRow->{'Create Table'} ?? null;
            if ($createSql) {
                DB::statement("USE `{$dbName}`");
                DB::statement($createSql);
            }
        }

        // Return connection details to be stored on Client row
        return [
            'db_name' => $dbName,
            'db_host' => env('DB_HOST'),
            'db_port' => env('DB_PORT', 3306),
            'db_username' => env('DB_USERNAME'),
            'db_password' => env('DB_PASSWORD'),
        ];
    }

    /**
     * Drop a tenant database if it exists. Safety checks to avoid dropping main DB.
     */
    public static function dropDatabase(?string $dbName): void
    {
        if (!$dbName) {
            return;
        }
        $dbName = trim($dbName);
        $mainDb = env('DB_DATABASE');
        if (!$dbName || $dbName === $mainDb) {
            return; // never drop the main database
        }
        // Ensure valid identifier (alphanumeric and underscore only)
        if (!preg_match('/^[A-Za-z0-9_\-]+$/', $dbName)) {
            return;
        }
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        } catch (\Throwable $e) {
            // best-effort only
        }
    }

    /**
     * Create a tenant database from a SQL dump file.
     * Returns the connection credentials array similar to createDraftDatabase().
     */
    public static function createDatabaseFromSqlDump(string $businessName, ?string $dumpPath = null): array
    {
        // Desired pattern: smartprep_<firstword> (e.g. "brian review center" => smartprep_brian)
        $fullSlug = Str::slug($businessName, '_');
        $firstWord = Str::before($fullSlug, '_');
        $core = substr($firstWord ?: ($fullSlug ?: 'site'), 0, 32);
        $baseName = 'smartprep_' . $core; // do not include rest of words

        $exists = function (string $name) {
            return (bool) DB::selectOne('SELECT SCHEMA_NAME as s FROM information_schema.schemata WHERE SCHEMA_NAME = ?', [$name]);
        };

        // Ensure uniqueness by appending incremental integer (smartprep_brian2, smartprep_brian3, ...)
        $dbName = $baseName;
        $counter = 2;
        while ($exists($dbName) && $counter < 50) { // safety cap
            $dbName = $baseName . $counter; // no underscore to keep it clean
            $counter++;
        }
        if ($exists($dbName)) { // fallback randomness if too many existing
            $dbName = $baseName . '_' . Str::lower(Str::random(4));
        }

        // 1) Create database
        DB::statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $originalDb = config('database.connections.mysql.database');
        DB::statement("USE `{$dbName}`");
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 2) Load SQL dump (structure only) and execute sequentially to respect order
        $dumpPath = $dumpPath ?: env('SAMPLE_WEBSITE_SQL_PATH', 'c:\\Users\\User\\Downloads\\smartprep_sample-website.sql');
        if (!is_readable($dumpPath)) {
            throw new \RuntimeException("Sample website SQL dump not found or unreadable at: {$dumpPath}");
        }

        $sql = file_get_contents($dumpPath);
        if ($sql === false) {
            throw new \RuntimeException('Failed to read SQL dump file.');
        }
        $sql = self::normalizeLineEndings($sql);

        // First attempt: robust split
        $executed = self::executeSqlDump($sql, $dbName);

        // If no tables created, attempt fallback naive splitter (maybe formatting edge case)
        $countAfterFirst = self::countTables($dbName);
        if ($countAfterFirst === 0) {
            logger()->warning("TenantProvisioner: first import pass created 0 tables for {$dbName}, attempting fallback parser");
            $executed += self::executeSqlDumpNaive($sql, $dbName);
        }

        // Table count verification (expecting ~57 tables from sample dump)
        $expected = (int) env('SAMPLE_DB_EXPECTED_TABLES', 57); // configurable
        $actual = self::countTables($dbName);
        if ($actual < $expected) {
            // If missing tables, raise an exception early to surface provisioning issue
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::statement("USE `{$originalDb}`");
            throw new \RuntimeException("Provisioned database '{$dbName}' has {$actual} tables; expected at least {$expected}. Check SQL dump integrity or parser.");
        }

        // Restore FK checks and original database
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::statement("USE `{$originalDb}`");

        return [
            'db_name' => $dbName,
            'db_host' => env('DB_HOST'),
            'db_port' => env('DB_PORT', 3306),
            'db_username' => env('DB_USERNAME'),
            'db_password' => env('DB_PASSWORD'),
        ];
    }
    /* -------------------------- Internal helper methods -------------------------- */

    private static function normalizeLineEndings(string $sql): string
    {
        return str_replace(["\r\n", "\r"], "\n", $sql);
    }

    private static function executeSqlDump(string $sql, string $dbName): int
    {
        $statements = [];
        $buffer = '';
        $inSingle = false; $inDouble = false; $inLineComment = false;
        $len = strlen($sql);
        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $next2 = $i + 1 < $len ? $sql[$i + 1] : '';

            // Line comments
            if (!$inSingle && !$inDouble) {
                if (!$inLineComment && $char === '-' && $next2 === '-' ) {
                    $inLineComment = true; $i++; continue;
                }
                if (!$inLineComment && $char === '#') { $inLineComment = true; continue; }
            }
            if ($inLineComment) {
                if ($char === "\n") { $inLineComment = false; }
                continue;
            }
            // Block / conditional comments
            if (!$inSingle && !$inDouble && $char === '/' && $next2 === '*') {
                $endPos = strpos($sql, '*/', $i + 2);
                $commentText = substr($sql, $i, ($endPos !== false ? $endPos + 2 : $len) - $i);
                if (str_starts_with($commentText, '/*!')) {
                    $inner = trim(substr($commentText, 3, -2));
                    $buffer .= $inner . "\n";
                }
                $i = ($endPos !== false ? $endPos + 1 : $len - 1);
                continue;
            }

            if ($char === "'" && !$inDouble) { $inSingle = !$inSingle; }
            elseif ($char === '"' && !$inSingle) { $inDouble = !$inDouble; }

            if ($char === ';' && !$inSingle && !$inDouble) {
                $stmt = trim($buffer);
                if ($stmt !== '' && stripos($stmt, 'CREATE DATABASE') === false) {
                    $statements[] = $stmt;
                }
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }
        $stmt = trim($buffer);
        if ($stmt !== '' && stripos($stmt, 'CREATE DATABASE') === false) { $statements[] = $stmt; }

        $executed = 0;
        foreach ($statements as $statement) {
            try {
                DB::unprepared($statement . ';');
                $executed++;
            } catch (\Throwable $e) {
                logger()->error('TenantProvisioner SQL error', [
                    'db' => $dbName,
                    'error' => $e->getMessage(),
                    'snippet' => substr($statement, 0, 200)
                ]);
            }
        }
        return $executed;
    }

    private static function executeSqlDumpNaive(string $sql, string $dbName): int
    {
        $parts = preg_split('/;\s*\n/', $sql);
        $executed = 0;
        foreach ($parts as $raw) {
            $statement = trim($raw);
            if ($statement === '' || stripos($statement, 'CREATE DATABASE') !== false) { continue; }
            if (preg_match('/^(--|#)/', $statement)) { continue; }
            if (str_starts_with($statement, '/*') && !str_starts_with($statement, '/*!')) { continue; }
            try {
                DB::unprepared($statement . ';');
                $executed++;
            } catch (\Throwable $e) {
                logger()->error('TenantProvisioner fallback SQL error', [
                    'db' => $dbName,
                    'error' => $e->getMessage(),
                    'snippet' => substr($statement, 0, 200)
                ]);
            }
        }
        return $executed;
    }

    private static function countTables(string $dbName): int
    {
        $row = DB::selectOne('SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = ?', [$dbName]);
        return (int) ($row->c ?? 0);
    }
}


