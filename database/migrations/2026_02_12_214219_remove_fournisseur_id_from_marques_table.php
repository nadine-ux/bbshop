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
        Schema::table('marques', function (Blueprint $table) {
            // Supprimer d'abord la clé étrangère
            $table->dropForeign(['fournisseur_id']);
            // Puis supprimer la colonne
            $table->dropColumn('fournisseur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marques', function (Blueprint $table) {
            // Recréer la colonne si on fait un rollback
            $table->unsignedBigInteger('fournisseur_id')->nullable();
            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('set null');
        });
    }
};