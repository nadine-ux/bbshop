<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
        });

        // Donne un username aux comptes existants (partie avant le @ de l'email)
        foreach (DB::table('users')->get() as $u) {
            $base = Str::slug(Str::before($u->email ?? ('user' . $u->id), '@'));
            $username = DB::table('users')->where('username', $base)->exists() ? $base . $u->id : $base;
            DB::table('users')->where('id', $u->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};