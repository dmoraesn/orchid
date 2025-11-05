<?php

declare(strict_types=1);

use App\Orchid\Screens\DashboardScreen;
use App\Orchid\Screens\OpportunityListScreen;
use App\Orchid\Screens\OpportunityEditScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\ImovelListScreen;
use App\Orchid\Screens\ImovelEditScreen;
use App\Orchid\Screens\VendedorListScreen;
use App\Orchid\Screens\VendedorEditScreen;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ORCHID PLATFORM ROUTES
|--------------------------------------------------------------------------
| Todas as rotas do painel administrativo (prefixo /admin)
| Middleware: web, platform, cache.headers, breadcrumbs
|--------------------------------------------------------------------------
*/

// =================================================================================
// DASHBOARD PRINCIPAL
// =================================================================================

Route::screen('dashboard', DashboardScreen::class)
    ->name('platform.dashboard')
    ->methods(['GET']);

// =================================================================================
// ROTA PRINCIPAL E PERFIL
// =================================================================================

Route::screen('main', PlatformScreen::class)
    ->name('platform.main');

Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile');

// =================================================================================
// OPORTUNIDADES (CRM CORE)
// =================================================================================

// Kanban + Filtros + Drag & Drop
Route::screen('oportunidades', OpportunityListScreen::class)
    ->name('platform.opportunity.list')
    ->methods(['GET', 'POST']);

// Criação de nova oportunidade
Route::screen('oportunidades/create', OpportunityEditScreen::class)
    ->name('platform.opportunity.create');

// Edição de oportunidade (com atividades, anexos, histórico)
Route::screen('oportunidade/{opportunity}', OpportunityEditScreen::class)
    ->name('platform.opportunity.edit');

// AJAX: Atualizar etapa via drag-and-drop
Route::post('oportunidades/update-stage', [OpportunityListScreen::class, 'updateStage'])
    ->name('platform.opportunity.update_stage');

// Excluir atividade (via modal na tela de edição)
Route::delete('oportunidade/{opportunity}/atividade/{activity}', [OpportunityEditScreen::class, 'removeActivity'])
    ->name('platform.opportunity.activity.remove');

// =================================================================================
// GESTÃO DE IMÓVEIS
// =================================================================================

Route::screen('imoveis', ImovelListScreen::class)
    ->name('platform.imoveis.list');

Route::screen('imovel/{imovel?}', ImovelEditScreen::class)
    ->name('platform.imovel.edit');

// =================================================================================
// GESTÃO DE VENDEDORES / CONSTRUTORAS
// =================================================================================

Route::screen('vendedores', VendedorListScreen::class)
    ->name('platform.vendedores.list');

Route::screen('vendedor/{vendedor?}', VendedorEditScreen::class)
    ->name('platform.vendedores.edit');

// =================================================================================
// SISTEMA (USUÁRIOS E PAPÉIS)
// =================================================================================

// Usuários
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users');

Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create');

Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit');

// Papéis
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles');

Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create');

Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit');

// =================================================================================
// ROTAS DE EXEMPLO / LEGACY (REMOVA SE NÃO USAR)
// =================================================================================

// Exemplo: Route::screen('example', ExampleScreen::class)->name('platform.example');