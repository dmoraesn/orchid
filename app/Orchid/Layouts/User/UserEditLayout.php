<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Nome')
                ->placeholder('Seu nome completo'),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title('E-mail')
                ->placeholder('seu@email.com'),
        ];
    }
}
