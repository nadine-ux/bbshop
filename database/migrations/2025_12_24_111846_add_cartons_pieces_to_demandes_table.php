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
    Schema::table('demandes', function (Blueprint $table) {
        $table->integer('quantite_cartons')->default(0);
        $table->integer('quantite_pieces')->default(0);
        $table->integer('quantite_total')->default(0);
    });
}

public function down(): void
{
    Schema::table('demandes', function (Blueprint $table) {
        $table->dropColumn(['quantite_cartons','quantite_pieces','quantite_total']);
    });
}

};
