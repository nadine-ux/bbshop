<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaires', function (Blueprint $table) {

            // ✅ modifier ENUM existant
            $table->enum('type', [
                'entree',
                'sortie',
                'ajustement',
                'inventaire_initial'
            ])->change();

            // (optionnel) autres modifications sûres
            if (!Schema::hasColumn('inventaires', 'date_mouvement')) {
                $table->timestamp('date_mouvement')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventaires', function (Blueprint $table) {

            // remettre l'ancien ENUM si besoin
            $table->enum('type', [
                'entree',
                'sortie'
            ])->change();
        });
    }
};
