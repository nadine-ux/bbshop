<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                  ->constrained('articles')
                  ->onDelete('cascade');
            $table->string('code_barres')->unique();
            $table->string('label')->nullable();   // ex: "Carton", "Pièce", "Palette"
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('article_id');
            $table->index('code_barres');
        });

        // Migrer l'ancien champ articles.code_barres vers la nouvelle table
        // À exécuter après la migration si la colonne existe déjà :
        // DB::statement("
        //     INSERT INTO article_barcodes (article_id, code_barres, is_primary, created_at, updated_at)
        //     SELECT id, code_barres, 1, NOW(), NOW()
        //     FROM articles
        //     WHERE code_barres IS NOT NULL AND code_barres != ''
        // ");

        // Ensuite supprimer la colonne de la table articles :
        // Schema::table('articles', function (Blueprint $table) {
        //     $table->dropColumn('code_barres');
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_barcodes');
    }
};