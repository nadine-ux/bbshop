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
    Schema::create('article_sortie', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sortie_id')->constrained()->onDelete('cascade');
        $table->foreignId('article_id')->constrained()->onDelete('cascade');
        $table->integer('quantite_cartons')->default(0);
        $table->integer('quantite_pieces')->default(0);
        $table->integer('quantite_total')->default(0); // toujours en pièces
        $table->timestamps();
    });
}


    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorties');
    }
};
