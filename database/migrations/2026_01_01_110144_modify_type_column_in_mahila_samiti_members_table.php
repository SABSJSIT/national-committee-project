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
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            // Change type column from enum to string to accommodate longer values
            $table->string('type', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            // Revert back to enum (note: this will truncate existing data that doesn't fit the enum values)
            $table->enum('type', ['pst', 'vp-sec', 'sanyojika', 'ksm_members'])->change();
        });
    }
};
