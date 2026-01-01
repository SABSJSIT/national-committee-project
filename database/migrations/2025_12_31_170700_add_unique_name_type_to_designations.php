<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: Composite unique validation is handled at application level in DesignationController
     * due to MySQL key length limitations with utf8mb4 encoding
     */
    public function up(): void
    {
        // No database changes needed - validation is handled at application level
        // in DesignationController to allow same name in different designation types
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
