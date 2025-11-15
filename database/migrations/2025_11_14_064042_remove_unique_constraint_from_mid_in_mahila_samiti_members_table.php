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
            // Remove the unique constraint from mid column
            $table->dropUnique(['mid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            // Add back the unique constraint to mid column
            $table->unique('mid');
        });
    }
};
