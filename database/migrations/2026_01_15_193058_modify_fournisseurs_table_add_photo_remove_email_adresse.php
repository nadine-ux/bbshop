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
        Schema::table('fournisseurs', function (Blueprint $table) {
            // Supprimer les colonnes email et adresse
            $table->dropColumn(['email', 'adresse']);
            
            // Ajouter la colonne photo
            $table->string('photo')->nullable()->after('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            // Remettre les colonnes supprimées
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            
            // Supprimer la colonne photo
            $table->dropColumn('photo');
        });
    }
};