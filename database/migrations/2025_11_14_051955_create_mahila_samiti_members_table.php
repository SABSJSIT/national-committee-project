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
        Schema::create('mahila_samiti_members', function (Blueprint $table) {
            $table->id();
            $table->string('session', 50);
            $table->string('anchal_name', 100);
            $table->string('anchal_code', 50);
            $table->enum('type', ['pst', 'vp-sec', 'sanyojika', 'ksm_members']);
            $table->string('designation', 100);
            $table->string('mid', 50)->unique();
            $table->string('name', 100);
            $table->string('husband_name', 100)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->text('address');
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('pincode', 10);
            $table->string('mobile_number', 15);
            $table->string('wtp_number', 15)->nullable();
            $table->string('photo')->nullable();
            $table->string('ex_post', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Create index for session for faster queries
            $table->index('session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahila_samiti_members');
    }
};
