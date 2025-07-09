<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddBatchFieldsToFormRequirements extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Add batch selection field
        DB::table('form_requirements')->insert([
            'field_name' => 'batch_id',
            'field_label' => 'Batch',
            'field_type' => 'select',
            'entity_type' => 'student',
            'program_type' => 'both',
            'is_required' => 0,
            'is_active' => 1,
            'is_bold' => 0,
            'field_options' => json_encode([
                'dynamic' => true,
                'source' => 'batches',
                'depends_on' => ['program_id', 'learning_mode']
            ]),
            'sort_order' => 7,
            'created_at' => now(),
            'updated_at' => now(),
            'section_name' => 'Student Information'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('form_requirements')
            ->where('field_name', 'batch_id')
            ->delete();
    }
}
