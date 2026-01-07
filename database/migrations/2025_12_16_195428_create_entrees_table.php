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
       Schema::create('entrees', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('fournisseur_id')->nullable();
        $table->date('date_reception');
        $table->string('numero_bon')->unique();
        $table->text('commentaire')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrees');
    }
};
