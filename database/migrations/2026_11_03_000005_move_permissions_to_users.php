<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Permissões passam a ser por usuário e por ação; o perfil vira só o nível, como no mocassessoria. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->jsonb('permissions')->default('{}');
        });

        $split = ['messages' => ['messages', 'subscribers', 'newsletters']];
        $all = ['acessar' => true, 'incluir' => true, 'editar' => true, 'excluir' => true];

        foreach (DB::table('roles')->where('full_access', false)->get() as $role) {
            $perms = [];
            foreach (json_decode($role->permissions ?? '[]', true) ?: [] as $module) {
                foreach ($split[$module] ?? [$module] as $key) {
                    $perms[$key] = $all;
                }
            }
            DB::table('users')->where('role_id', $role->id)->update(['permissions' => json_encode($perms ?: new stdClass)]);
        }

        Schema::table('roles', function (Blueprint $t) {
            $t->dropColumn('permissions');
        });

        if (! DB::table('roles')->where('name', 'Usuário')->exists()) {
            DB::table('roles')->insert(['name' => 'Usuário', 'full_access' => false, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', fn (Blueprint $t) => $t->jsonb('permissions')->default('[]'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('permissions'));
    }
};
