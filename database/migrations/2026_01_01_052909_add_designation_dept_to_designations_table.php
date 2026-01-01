<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->enum('designation_dept', ['shree_sangh', 'mahila_samiti', 'yuva_sangh', 'all'])
                  ->default('all')
                  ->after('designation_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('designation_dept');
        });
    }
};
