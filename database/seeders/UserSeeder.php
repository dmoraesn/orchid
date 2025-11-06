<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Obtém os papéis criados no RolesAndPermissionsSeeder
        $adminRole = Role::firstOrCreate(['slug' => 'administrator'], [
            'name' => 'Administrador Global',
            'permissions' => ['platform.index' => true],
        ]);

        $corretorRole = Role::firstOrCreate(['slug' => 'corretor'], [
            'name' => 'Corretor de Vendas',
            'permissions' => ['platform.index' => true],
        ]);

        // 🔹 1. Usuário Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@crm.com'],
            [
                'name'        => 'Admin do CRM',
                'password'    => Hash::make('password'),
                'permissions' => ['platform.index' => true],
            ]
        );

        // Vincula papel se não estiver vinculado
        if (! $admin->roles()->where('id', $adminRole->id)->exists()) {
            $admin->addRole($adminRole);
        }

        // 🔹 2. Usuário Corretor 1
        $corretor1 = User::firstOrCreate(
            ['email' => 'corretor1@crm.com'],
            [
                'name'        => 'Ana Corretora',
                'password'    => Hash::make('password'),
                'permissions' => ['platform.index' => true],
            ]
        );

        if (! $corretor1->roles()->where('id', $corretorRole->id)->exists()) {
            $corretor1->addRole($corretorRole);
        }

        // 🔹 3. Usuário Corretor 2
        $corretor2 = User::firstOrCreate(
            ['email' => 'corretor2@crm.com'],
            [
                'name'        => 'Bruno Corretor',
                'password'    => Hash::make('password'),
                'permissions' => ['platform.index' => true],
            ]
        );

        if (! $corretor2->roles()->where('id', $corretorRole->id)->exists()) {
            $corretor2->addRole($corretorRole);
        }

        $this->command->info('✅ Usuários e papéis configurados:');
        $this->command->info('→ Admin: admin@crm.com / password');
        $this->command->info('→ Corretores: corretor1@crm.com, corretor2@crm.com / password');
    }
}
