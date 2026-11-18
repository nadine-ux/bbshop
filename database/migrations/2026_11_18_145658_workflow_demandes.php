<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quantité voulue par article
        if (!Schema::hasColumn('articles', 'quantite_voulue')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->unsignedInteger('quantite_voulue')->default(0)->after('stock');
            });
        }

        // 2. Nouvelles colonnes sur demandes
        Schema::table('demandes', function (Blueprint $table) {
            if (!Schema::hasColumn('demandes', 'gestionnaire_id')) {
                $table->unsignedBigInteger('gestionnaire_id')->nullable()->after('employe_id');
            }
            if (!Schema::hasColumn('demandes', 'motif_refus')) {
                $table->text('motif_refus')->nullable();
            }
            if (!Schema::hasColumn('demandes', 'date_validation')) {
                $table->timestamp('date_validation')->nullable();
            }
            if (!Schema::hasColumn('demandes', 'date_reception')) {
                $table->timestamp('date_reception')->nullable();
            }
        });

        // 3. Statut "recue" + anciennes colonnes article rendues facultatives
        DB::statement("ALTER TABLE demandes MODIFY statut ENUM('en_attente','validee','refusee','recue') NOT NULL DEFAULT 'en_attente'");
        DB::statement("ALTER TABLE demandes MODIFY article_id BIGINT UNSIGNED NULL");
        if (Schema::hasColumn('demandes', 'quantite')) {
            DB::statement("ALTER TABLE demandes MODIFY quantite INT NOT NULL DEFAULT 0");
        }

        // 4. Table des lignes
        Schema::create('demande_lignes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demande_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('quantite_cartons')->default(0);
            $table->unsignedInteger('quantite_pieces')->default(0);
            $table->unsignedInteger('quantite_total')->default(0);
            $table->timestamps();

            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
            $table->foreign('article_id')->references('id')->on('articles');
        });

        // 5. Reprise des anciennes demandes (1 article) dans les lignes
        if (Schema::hasColumn('demandes', 'quantite_total')) {
            DB::statement("
                INSERT INTO demande_lignes (demande_id, article_id, quantite_cartons, quantite_pieces, quantite_total, created_at, updated_at)
                SELECT id, article_id, quantite_cartons, quantite_pieces, quantite_total, NOW(), NOW()
                FROM demandes
                WHERE article_id IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_lignes');
    }
};