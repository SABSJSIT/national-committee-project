<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHindiFieldsToMahilaSamitiMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            $table->string('name_hindi', 100)->nullable()->after('name');
            $table->string('father_name_hindi', 100)->nullable()->after('father_name');
            $table->text('address_hindi')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mahila_samiti_members', function (Blueprint $table) {
            $table->dropColumn(['name_hindi', 'father_name_hindi', 'address_hindi']);
        });
    }
}