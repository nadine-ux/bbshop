<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        // ---------- permissions ----------
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->engine = 'MyISAM';

            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('guard_name', 50);
            $table->timestamps();

            $table->unique(['name', 'guard_name'], 'permissions_name_guard_unique');
        });

        // ---------- roles ----------
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->engine = 'MyISAM';

            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('guard_name', 50);
            $table->timestamps();

            $table->unique(['name', 'guard_name'], 'roles_name_guard_unique');
        });

        // ---------- model_has_permissions ----------
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->engine = 'MyISAM';

            $table->unsignedBigInteger('permission_id');
            $table->string('model_type', 100);
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_permissions_index');
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_primary');
        });

        // ---------- model_has_roles ----------
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames) {
            $table->engine = 'MyISAM';

            $table->unsignedBigInteger('role_id');
            $table->string('model_type', 100);
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_roles_index');
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_primary');
        });

        // ---------- role_has_permissions ----------
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->engine = 'MyISAM';

            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_primary');
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
