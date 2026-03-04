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
    Schema::table('entrees', function (Blueprint $table) {
        $table->decimal('remise_globale', 5, 2)->default(0)->after('commentaire');
    });
}

public function down(): void
{
    Schema::table('entrees', function (Blueprint $table) {
        $table->dropColumn('remise_globale');
    });
}
};
