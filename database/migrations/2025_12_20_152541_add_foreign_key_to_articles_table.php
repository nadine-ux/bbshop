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
        Schema::table('articles', function (Blueprint $table) {
        // Ajout de la contrainte clé étrangère
        $table->foreign('categorie_id')
              ->references('id')
              ->on('categories')
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    Schema::table('articles', function (Blueprint $table) {
        $table->dropForeign(['categorie_id']);
    });
    }
};
