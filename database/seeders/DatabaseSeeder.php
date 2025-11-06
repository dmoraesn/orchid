<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Executa todos os seeders necessários para a aplicação.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando o processo de seed do banco de dados...');

        // 1️⃣ Papéis e Permissões básicas
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
        $this->command->info('✅ Roles e permissões básicas criadas.');

        // 2️⃣ Usuários padrão (Admin, Corretor, etc.)
        $this->call([
            UserSeeder::class,
        ]);
        $this->command->info('✅ Usuários padrão criados.');

        // 3️⃣ Permissões específicas (como o acesso ao Kanban de Oportunidades)
        $this->call([
            OpportunityPermissionSeeder::class,
        ]);
        $this->command->info('✅ Permissão "platform.opportunity.list" aplicada ao papel admin.');

        // 4️⃣ Usuário genérico de teste (opcional)
        if (!User::where('email', 'generico@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Teste Genérico',
                'email' => 'generico@example.com',
            ]);
            $this->command->info('👤 Usuário genérico criado.');
        } else {
            $this->command->warn('ℹ️ Usuário genérico já existe — não foi recriado.');
        }

        $this->command->info('🎯 Seed finalizado com sucesso!');
    }
}
