<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\FormRequirement;

class ManageDynamicFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registration:manage-fields {action} {field_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage dynamic registration fields (add, remove, list)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $fieldName = $this->argument('field_name');

        switch ($action) {
            case 'add':
                return $this->addField($fieldName);
            case 'remove':
                return $this->removeField($fieldName);
            case 'list':
                return $this->listFields();
            default:
                $this->error('Invalid action. Use: add, remove, or list');
                return Command::FAILURE;
        }
    }

    /**
     * Add a new field to the registrations table
     */
    private function addField($fieldName)
    {
        if (!$fieldName) {
            $fieldName = $this->ask('Enter the field name:');
        }

        if (Schema::hasColumn('registrations', $fieldName)) {
            $this->error("Field '{$fieldName}' already exists in the registrations table.");
            return Command::FAILURE;
        }

        $fieldType = $this->choice('Select field type:', [
            'string' => 'String',
            'text' => 'Text',
            'integer' => 'Integer',
            'decimal' => 'Decimal',
            'boolean' => 'Boolean',
            'date' => 'Date',
            'datetime' => 'DateTime',
            'json' => 'JSON'
        ]);

        $nullable = $this->confirm('Should this field be nullable?', true);

        // Create migration content
        $migrationContent = $this->generateMigration($fieldName, $fieldType, $nullable);
        
        // Create migration file
        $migrationFile = database_path('migrations/' . date('Y_m_d_His') . '_add_' . $fieldName . '_to_registrations_table.php');
        file_put_contents($migrationFile, $migrationContent);

        $this->info("Migration created: {$migrationFile}");
        $this->info("Run 'php artisan migrate' to apply the changes.");
        
        // Ask if they want to add it to form requirements
        if ($this->confirm('Add this field to form requirements?', true)) {
            $this->addToFormRequirements($fieldName);
        }

        return Command::SUCCESS;
    }

    /**
     * Remove a field from active use (disable it)
     */
    private function removeField($fieldName)
    {
        if (!$fieldName) {
            $fieldName = $this->ask('Enter the field name to disable:');
        }

        $requirement = FormRequirement::where('field_name', $fieldName)->first();
        
        if (!$requirement) {
            $this->error("Field '{$fieldName}' not found in form requirements.");
            return Command::FAILURE;
        }

        $requirement->is_active = false;
        $requirement->save();

        $this->info("Field '{$fieldName}' has been disabled. Data is preserved but field won't appear in forms.");
        return Command::SUCCESS;
    }

    /**
     * List all fields
     */
    private function listFields()
    {
        $requirements = FormRequirement::orderBy('sort_order')->get();
        
        $this->table(
            ['Field Name', 'Label', 'Type', 'Program', 'Required', 'Active', 'Sort Order'],
            $requirements->map(function ($req) {
                return [
                    $req->field_name,
                    $req->field_label,
                    $req->field_type,
                    $req->program_type,
                    $req->is_required ? 'Yes' : 'No',
                    $req->is_active ? 'Yes' : 'No',
                    $req->sort_order
                ];
            })
        );

        return Command::SUCCESS;
    }

    /**
     * Generate migration content
     */
    private function generateMigration($fieldName, $fieldType, $nullable)
    {
        $nullableStr = $nullable ? '->nullable()' : '';
        
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint \$table) {
            if (!Schema::hasColumn('registrations', '{$fieldName}')) {
                \$table->{$fieldType}('{$fieldName}'){$nullableStr};
            }
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint \$table) {
            // We don't drop columns to preserve historical data
        });
    }
};";
    }

    /**
     * Add field to form requirements
     */
    private function addToFormRequirements($fieldName)
    {
        $label = $this->ask('Enter field label:');
        $type = $this->choice('Select field type:', [
            'text', 'email', 'tel', 'date', 'file', 'select', 'textarea', 'checkbox', 'radio', 'number'
        ]);
        $programType = $this->choice('Program type:', ['complete', 'modular', 'both']);
        $required = $this->confirm('Is this field required?', false);

        FormRequirement::create([
            'field_name' => $fieldName,
            'field_label' => $label,
            'field_type' => $type,
            'program_type' => $programType,
            'is_required' => $required,
            'is_active' => true,
            'sort_order' => FormRequirement::max('sort_order') + 1
        ]);

        $this->info("Field '{$fieldName}' added to form requirements.");
    }
}
