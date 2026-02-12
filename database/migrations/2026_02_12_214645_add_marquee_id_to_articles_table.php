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
            $table->unsignedBigInteger('marque_id')->nullable()->after('categorie_id');
            $table->foreign('marque_id')->references('id')->on('marques')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['marque_id']);
            $table->dropColumn('marque_id');
        });
    }
};
