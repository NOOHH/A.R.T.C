<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE form_requirements MODIFY COLUMN field_type ENUM('text','email','tel','date','file','select','textarea','checkbox','radio','number','section','module_selection')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE form_requirements MODIFY COLUMN field_type ENUM('text','email','tel','date','file','select','textarea','checkbox','radio','number','section')");
    }
};
