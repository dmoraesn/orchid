<?php

declare(strict_types=1);

use App\Orchid\Screens\{
    DashboardScreen,
    ImovelEditScreen,
    ImovelListScreen,
    OpportunityEditScreen,
    OpportunityListScreen,
    PlatformScreen,
    VendedorEditScreen,
    VendedorListScreen,
    User\UserEditScreen,
    User\UserListScreen,
    User\UserProfileScreen,
    Role\RoleEditScreen,
    Role\RoleListScreen,
};

use Illuminate\Support\Facades\Route;

// DASHBOARD
Route::screen('dashboard', DashboardScreen::class)
    ->name('platform.dashboard')
    ->methods(['GET']);

// PÁGINA INICIAL & PERFIL
Route::screen('main', PlatformScreen::class)->name('platform.main');
Route::screen('profile', UserProfileScreen::class)->name('platform.profile');

// OPORTUNIDADES
Route::screen('oportunidades', OpportunityListScreen::class)
    ->name('platform.opportunity.list')
    ->methods(['GET', 'POST']);

Route::screen('oportunidades/create', OpportunityEditScreen::class)
    ->name('platform.opportunity.create');

Route::screen('oportunidade/{opportunity}', OpportunityEditScreen::class)
    ->name('platform.opportunity.edit');

Route::post('oportunidades/update-stage', [OpportunityListScreen::class, 'updateStage'])
    ->name('platform.opportunity.update_stage');

Route::get('oportunidades/load-more', [OpportunityListScreen::class, 'loadMore'])
    ->name('platform.opportunity.load_more');

// IMÓVEIS
Route::screen('imoveis', ImovelListScreen::class)->name('platform.imoveis.list');
Route::screen('imovel/{imovel?}', ImovelEditScreen::class)->name('platform.imovel.edit');

// VENDEDORES
Route::screen('vendedores', VendedorListScreen::class)->name('platform.vendedores.list');
Route::screen('vendedor/{vendedor?}', VendedorEditScreen::class)->name('platform.vendedores.edit');

// USUÁRIOS
Route::screen('users', UserListScreen::class)->name('platform.systems.users');
Route::screen('users/create', UserEditScreen::class)->name('platform.systems.users.create');
Route::screen('users/{user}/edit', UserEditScreen::class)->name('platform.systems.users.edit');

// PAPÉIS
Route::screen('roles', RoleListScreen::class)->name('platform.systems.roles');
Route::screen('roles/create', RoleEditScreen::class)->name('platform.systems.roles.create');
Route::screen('roles/{role}/edit', RoleEditScreen::class)->name('platform.systems.roles.edit');
