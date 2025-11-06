<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Opportunity;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
        // Você pode adicionar lógicas de inicialização aqui (ex: registrar badges globais)
    }

    /**
     * Registra o menu lateral principal.
     */
    public function menu(): array
    {
        return [

            // -----------------------------------------------------
            // 🏠 SEÇÃO: NAVEGAÇÃO GERAL
            // -----------------------------------------------------
            Menu::make('Painel Principal')
                ->icon('bs.house')
                ->title('Navegação')
                ->route(config('platform.index'))
                ->permission('platform.index'),

            // -----------------------------------------------------
            // 🏢 SEÇÃO: GESTÃO IMOBILIÁRIA / CRM
            // -----------------------------------------------------

            // Kanban de Oportunidades
            Menu::make('Kanban de Oportunidades')
                ->icon('bs.columns-gap')
                ->route('platform.opportunity.list')
                ->title('Gestão Imobiliária')
                ->permission('platform.opportunity.list')
                ->sort(90)
                ->badge(fn ()
                => Opportunity::where('etapa_pipeline', 'Novo Lead / Sem Atendimento')->count(), Color::INFO),

            // Imóveis
            Menu::make('Imóveis')
                ->icon('bs.house-door')
                ->route('platform.imoveis.list')
                ->permission('platform.imoveis')
                ->sort(100),

            // Vendedores / Construtoras
            Menu::make('Vendedores / Construtoras')
                ->icon('bs.person-rolodex')
                ->route('platform.vendedores.list')
                ->permission('platform.vendedores')
                ->sort(110)
                ->divider(),

            // -----------------------------------------------------
            // ⚙️ SEÇÃO: CONTROLE DE ACESSO
            // -----------------------------------------------------
            Menu::make(__('Usuários'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Controle de Acesso')),

            Menu::make(__('Papéis e Permissões'))
                ->icon('bs.shield-lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * Registra os grupos de permissões disponíveis no painel.
     */
    public function permissions(): array
    {
        return [

            // -----------------------------------------------------
            // 🔒 Permissões de Sistema
            // -----------------------------------------------------
            ItemPermission::group(__('Sistema'))
                ->addPermission('platform.systems.roles', __('Gerenciar Papéis'))
                ->addPermission('platform.systems.users', __('Gerenciar Usuários')),

            // -----------------------------------------------------
            // 🏢 Permissões do CRM Imobiliário
            // -----------------------------------------------------
            ItemPermission::group('Gestão Imobiliária / CRM')
                ->addPermission('platform.opportunity.list', 'Acesso ao Kanban de Oportunidades')
                ->addPermission('platform.imoveis', 'Acesso à Gestão de Imóveis')
                ->addPermission('platform.vendedores', 'Acesso à Gestão de Vendedores / Construtoras'),
        ];
    }
}
