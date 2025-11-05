<?php

namespace App\Orchid\Layouts\Activity;

use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class ActivityEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('activity.id')->type('hidden'),

            Select::make('activity.tipo')
                ->title('Tipo de Atividade')
                ->options([
                    'Ligação' => 'Ligação',
                    'Reunião' => 'Reunião',
                    'Visita' => 'Visita',
                    'E-mail' => 'E-mail',
                    'Proposta' => 'Proposta',
                ])
                ->required(),

            Input::make('activity.titulo')
                ->title('Título')
                ->placeholder('Ex: Reunião de Apresentação')
                ->required(),

            DateTimer::make('activity.data_agendada')
                ->title('Data e Hora')
                ->format('Y-m-d H:i')
                ->required(),

            Select::make('activity.status')
                ->title('Status')
                ->options(['Pendente', 'Concluída', 'Cancelada'])
                ->required(),

            Select::make('activity.user_id')
                ->title('Responsável')
                ->fromModel(\App\Models\User::class, 'name')
                ->required(),

            TextArea::make('activity.descricao')
                ->title('Descrição')
                ->rows(3),
        ];
    }
}