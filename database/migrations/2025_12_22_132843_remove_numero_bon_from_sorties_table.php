<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('sorties', function (Blueprint $table) {
        $table->dropColumn('numero_bon');
    });
}

public function down()
{
    Schema::table('sorties', function (Blueprint $table) {
        $table->string('numero_bon')->nullable();
    });
}

    
};
