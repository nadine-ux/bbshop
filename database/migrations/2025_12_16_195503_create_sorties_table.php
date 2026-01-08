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

            // clé étrangère vers sorties.id
            $table->unsignedBigInteger('sortie_id');
            $table->foreign('sortie_id')
                  ->references('id')
                  ->on('sorties')
                  ->onDelete('cascade');

            // clé étrangère vers articles.id
            $table->unsignedBigInteger('article_id');
            $table->foreign('article_id')
                  ->references('id')
                  ->on('articles')
                  ->onDelete('cascade');

            $table->integer('quantite_cartons')->default(0);
            $table->integer('quantite_pieces')->default(0);
            $table->integer('quantite_total')->default(0); // toujours en pièces
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_sortie');
    }
};
