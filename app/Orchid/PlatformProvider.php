<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Opportunity; // Importa o modelo Opportunity para o badge
use App\Orchid\Screens\OpportunityListScreen; // CORRIGIDO: Nome da Screen para Kanban
use App\Orchid\Screens\Vendedor; // Mantido, mas verifique o nome da classe
use App\Orchid\Screens\Imovel; // Mantido, mas verifique o nome da classe
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Painel Principal')
                ->icon('bs.book')
                ->title('Navegação')
                ->route(config('platform.index')),

            // ---------------------------------------------
            // GESTÃO IMOBILIÁRIA (CRM)
            // ---------------------------------------------
            
            // Oportunidades (Item Principal e Título da Seção)
            Menu::make('Oportunidades (Kanban)')
                ->icon('bs.columns-gap')
                ->route('platform.opportunity.list') // CORREÇÃO: Usa a rota definida no routes/platform.php
                ->title('Gestão Imobiliária') // Define o título da seção
                ->permission('platform.opportunities')
                ->sort(90)
                // Adiciona o badge para Leads 'Novo Lead / Sem Atendimento'
                ->badge(fn () => Opportunity::where('etapa_pipeline', 'Novo Lead / Sem Atendimento')->count(), Color::INFO),

            // Imóveis (Sub-item)
            Menu::make('Imóveis')
                ->icon('bs.house-door')
                ->route('platform.imoveis.list')
                ->permission('platform.imoveis')
                ->title('Gestão Imobiliária') // Usa o mesmo título da seção para agrupamento
                ->sort(110),
                
            // Vendedores / Construtoras (Sub-item)
            Menu::make('Vendedores / Construtoras')
                ->icon('bs.person-rolodex')
                ->route('platform.vendedores.list') 
                ->permission('platform.vendedores') 
                ->title('Gestão Imobiliária') // Usa o mesmo título da seção para agrupamento
                ->sort(120)
                ->divider(),

            // ---------------------------------------------
            // ACESSOS PADRÃO (Mantido)
            // ---------------------------------------------
            
            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            // Permissões do CRM (Mantidas)
            ItemPermission::group('Imobiliário')
                ->addPermission('platform.opportunities', 'Acesso ao Kanban de Oportunidades')
                ->addPermission('platform.vendedores', 'Acesso à Gestão de Vendedores')
                ->addPermission('platform.imoveis', 'Acesso à Gestão de Imóveis'),
        ];
    }
}