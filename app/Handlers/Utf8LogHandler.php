<?php

namespace App\Handlers;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class Utf8LogHandler extends RotatingFileHandler
{
    /**
     * Create a new UTF-8 log handler instance.
     *
     * @param string $filename
     * @param int $maxFiles
     * @param int $level
     * @param bool $filePermission
     * @param bool $useLocking
     */
    public function __construct($filename, $maxFiles = 0, $level = Logger::DEBUG, $filePermission = null, $useLocking = false)
    {
        parent::__construct($filename, $maxFiles, $level, $filePermission, $useLocking);
        
        // Set up UTF-8 formatter
        $this->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true, // Allow inline line breaks
            true  // Ignore empty context and extra
        ));
    }

    /**
     * Write the log record to the file with proper UTF-8 encoding.
     *
     * @param array $record
     */
    protected function write(array $record): void
    {
        // Ensure the message is properly encoded as UTF-8
        if (isset($record['message'])) {
            $record['message'] = $this->ensureUtf8($record['message']);
        }

        // Ensure context is properly encoded
        if (isset($record['context'])) {
            $record['context'] = $this->ensureUtf8Context($record['context']);
        }

        parent::write($record);
    }

    /**
     * Ensure a string is properly UTF-8 encoded.
     *
     * @param string $string
     * @return string
     */
    protected function ensureUtf8($string)
    {
        // If the string is not UTF-8, convert it
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'auto');
        }
        
        return $string;
    }

    /**
     * Ensure context array is properly UTF-8 encoded.
     *
     * @param array $context
     * @return array
     */
    protected function ensureUtf8Context($context)
    {
        $encoded = [];
        
        foreach ($context as $key => $value) {
            if (is_string($value)) {
                $encoded[$key] = $this->ensureUtf8($value);
            } elseif (is_array($value)) {
                $encoded[$key] = $this->ensureUtf8Context($value);
            } else {
                $encoded[$key] = $value;
            }
        }
        
        return $encoded;
    }
}
