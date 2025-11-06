<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class OpportunityPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Procura o papel admin
        $admin = Role::where('slug', 'admin')->first();

        if (!$admin) {
            $this->command->warn('⚠️ Papel "admin" não encontrado. Crie-o antes de rodar este seeder.');
            return;
        }

        // Adiciona a permissão ao papel admin
        $permissions = $admin->permissions ?? [];
        $permissions['platform.opportunity.list'] = true;

        $admin->permissions = $permissions;
        $admin->save();

        $this->command->info('✅ Permissão "platform.opportunity.list" adicionada ao papel Admin.');
    }
}
