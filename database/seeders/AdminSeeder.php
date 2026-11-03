<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /** Cria o primeiro administrador; a senha aleatória aparece uma única vez no terminal. */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Administrador'], ['full_access' => true, 'permissions' => []]);
        Role::firstOrCreate(['name' => 'Dev'], ['full_access' => true, 'permissions' => []]);
        $email = env('ADMIN_EMAIL', 'admin@forestark.com.br');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = Str::password(16, symbols: false);
        User::create(['name' => 'Administrador', 'email' => $email, 'password' => Hash::make($password), 'role_id' => $role->id, 'is_active' => true]);
        $this->command?->warn("Admin: {$email} / senha: {$password}");
    }
}
