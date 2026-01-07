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
        Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->unsignedBigInteger('categorie_id')->nullable();
        $table->string('code_barres')->unique();
        $table->string('photo')->nullable();
        $table->date('date_peremption')->nullable();
        $table->integer('quantite_minimale')->default(0);
        $table->decimal('prix_achat', 10, 2)->nullable();
        $table->unsignedBigInteger('fournisseur_id')->nullable();
        $table->text('description')->nullable();
        $table->integer('contenance_carton')->default(1);
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
