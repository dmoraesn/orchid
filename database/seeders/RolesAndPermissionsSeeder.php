<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;
use Orchid\Platform\Dashboard as OrchidDashboard;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param OrchidDashboard $dashboard
     */
    public function run(OrchidDashboard $dashboard): void
    {
        // ==========================================================
        // 1. Permissões Customizadas do Sistema
        // ==========================================================
        $customPermissions = [
            'platform.opportunity.list', // ✅ adicionada explicitamente
            'platform.opportunity.create',
            'platform.opportunity.edit',
            'platform.opportunity.update_stage',
            'platform.vendedores',
            'platform.imoveis',
        ];

        // Obtém permissões internas do Orchid
        $systemPermissions = collect($dashboard->getPermission())
            ->flatMap(fn($group) => $group['list'] ?? [])
            ->pluck('slug')
            ->unique()
            ->toArray();

        // ==========================================================
        // 2. Permissões por Papel
        // ==========================================================
        $adminPermissions = array_fill_keys(
            array_merge($systemPermissions, $customPermissions),
            true
        );

        $corretorPermissions = array_fill_keys($customPermissions, true);
        $corretorPermissions['platform.index'] = true;
        $corretorPermissions['platform.systems'] = false;
        $corretorPermissions['platform.systems.users'] = false;
        $corretorPermissions['platform.profile'] = true;

        // ==========================================================
        // 3. Criação dos Papéis
        // ==========================================================
        Role::updateOrCreate(['slug' => 'administrator'], [
            'name'        => 'Administrador Global',
            'permissions' => $adminPermissions,
        ]);

        Role::updateOrCreate(['slug' => 'imobiliaria'], [
            'name'        => 'Gerente de Imobiliária',
            'permissions' => $adminPermissions,
        ]);

        Role::updateOrCreate(['slug' => 'corretor'], [
            'name'        => 'Corretor de Vendas',
            'permissions' => $corretorPermissions,
        ]);
    }
}
