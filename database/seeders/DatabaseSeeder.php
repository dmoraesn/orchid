<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Opcional, mantendo a configuração original de ignorar eventos
    use WithoutModelEvents; 

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Chamar o seeder de Papéis/Permissões
        // Ele cria os Roles 'admin', 'imobiliaria' e 'corretor'.
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // 2. Chamar o seeder de Usuários
        // Ele cria os usuários 'admin@crm.com' e 'corretor1@crm.com' e os associa aos papéis.
        $this->call([
            UserSeeder::class,
        ]);


        // 3. (Opcional) Criação de um usuário genérico, se ainda necessário
        // Se o UserSeeder já cria o Admin, esta linha pode ser removida ou modificada.
        // Mantenho a estrutura original, mas ajusto a criação de um usuário de teste simples.
        User::factory()->create([
             'name' => 'Teste Genérico',
             'email' => 'generico@example.com',
        ]);
    }
}