<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaires', function (Blueprint $table) {
            // Supprimer la colonne bizarre si elle existe
            if (Schema::hasColumn('inventaires', 'dateecartes')) {
                $table->dropColumn('dateecartes');
            }
            
            // Ajouter les colonnes manquantes
            $table->integer('quantite')->after('type');
            $table->integer('stock_avant')->after('quantite');
            $table->integer('stock_apres')->after('stock_avant');
            $table->decimal('prix_unitaire', 10, 2)->nullable()->after('stock_apres');
            
            // Références
            $table->foreignId('entree_id')->nullable()->after('prix_unitaire')
                ->constrained('entrees')->onDelete('set null');
            $table->foreignId('sortie_id')->nullable()->after('entree_id')
                ->constrained('sorties')->onDelete('set null');
            
            // Informations complémentaires
            $table->string('motif')->nullable()->after('sortie_id');
            $table->text('commentaire')->nullable()->after('motif');
            $table->foreignId('user_id')->nullable()->after('commentaire')
                ->constrained('users')->onDelete('set null');
            
            // Index
            $table->index('article_id');
            $table->index('type');
            $table->index('date_mouvement');
        });
    }

    public function down(): void
    {
        Schema::table('inventaires', function (Blueprint $table) {
            $table->dropColumn([
                'quantite',
                'stock_avant',
                'stock_apres',
                'prix_unitaire',
                'entree_id',
                'sortie_id',
                'motif',
                'commentaire',
                'user_id'
            ]);
        });
    }
};