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
        // 1. Permissões Customizadas
        // ==========================================================
        $customPermissions = [
            'platform.opportunities',
            'platform.vendedores',
            'platform.imoveis',
        ];

        // CORREÇÃO 1: getPermission() (singular)
        // CORREÇÃO 2: $group é array, não objeto → use $group['list']
        $systemPermissions = collect($dashboard->getPermission())
            ->flatMap(fn ($group) => $group['list'] ?? []) // ← AQUI!
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
        $corretorPermissions['platform.systems.users'] = true;
        $corretorPermissions['platform.profile'] = true;

        // ==========================================================
        // 3. Criação dos Papéis
        // ==========================================================
        Role::firstOrCreate(['slug' => 'administrator'], [
            'name'        => 'Administrador Global',
            'permissions' => $adminPermissions,
        ]);

        Role::firstOrCreate(['slug' => 'imobiliaria'], [
            'name'        => 'Gerente de Imobiliária',
            'permissions' => $adminPermissions,
        ]);

        Role::firstOrCreate(['slug' => 'corretor'], [
            'name'        => 'Corretor de Vendas',
            'permissions' => $corretorPermissions,
        ]);
    }
}