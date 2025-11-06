<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class ProfilePasswordLayout extends Rows
{
    public function fields(): array
    {
        return [
            Password::make('old_password')
                ->title('Senha Atual')
                ->placeholder('Digite sua senha atual')
                ->required(),

            Password::make('password')
                ->title('Nova Senha')
                ->placeholder('Crie uma senha forte')
                ->required(),

            Password::make('password_confirmation')
                ->title('Confirmar Nova Senha')
                ->placeholder('Repita a nova senha')
                ->help('Use pelo menos 8 caracteres, com números e letras.'),
        ];
    }
}
