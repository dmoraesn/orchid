<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que os papéis existam (com os slugs CORRETOS)
        $adminRole = Role::where('slug', 'administrator')->firstOrFail(); // CORRIGIDO
        $corretorRole = Role::where('slug', 'corretor')->firstOrFail();   // OK

        // 1. Usuário Administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@crm.com',
        ], [
            'name'     => 'Admin do CRM',
            'password' => Hash::make('password'),
            'permissions' => [
                'platform.index' => true,
            ],
        ]);
        $admin->addRole($adminRole); // Agora $adminRole não é null


        // 2. Usuário Corretor 1
        $corretor1 = User::firstOrCreate([
            'email' => 'corretor1@crm.com',
        ], [
            'name'     => 'Ana Corretora',
            'password' => Hash::make('password'),
            'permissions' => [
                'platform.index' => true,
            ],
        ]);
        $corretor1->addRole($corretorRole);
        
        // 3. Usuário Corretor 2
        $corretor2 = User::firstOrCreate([
            'email' => 'corretor2@crm.com',
        ], [
            'name'     => 'Bruno Corretor',
            'password' => Hash::make('password'),
            'permissions' => [
                'platform.index' => true,
            ],
        ]);
        $corretor2->addRole($corretorRole);

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('→ admin@crm.com (Administrador)');
        $this->command->info('→ corretor1@crm.com, corretor2@crm.com (Corretores)');
        $this->command->info('Senha para todos: password');
    }
}