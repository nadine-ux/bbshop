<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_entree', function (Blueprint $table) {
            $table->decimal('remise', 5, 2)->default(0)->after('prix_unitaire');
        });
    }

    public function down(): void
    {
        Schema::table('article_entree', function (Blueprint $table) {
            $table->dropColumn('remise');
        });
    }
};