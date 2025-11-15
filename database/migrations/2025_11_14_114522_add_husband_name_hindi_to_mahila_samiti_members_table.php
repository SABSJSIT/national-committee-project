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
            $table->string('husband_name_hindi')->nullable()->after('husband_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            $table->dropColumn('husband_name_hindi');
        });
    }
};
