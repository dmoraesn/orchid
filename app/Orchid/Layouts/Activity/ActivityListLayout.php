<?php

namespace App\Orchid\Layouts\Activity;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ActivityListLayout extends Table
{
    protected $target = 'activities';

    protected function columns(): array
    {
        return [
            TD::make('tipo', 'Tipo')
                ->render(fn ($activity) => "<span class='badge bg-primary'>{$activity->tipo}</span>"),

            TD::make('titulo', 'Título'),

            TD::make('data_agendada', 'Data')
                ->render(fn ($activity) => $activity->data_agendada->format('d/m/Y H:i')),

            TD::make('status', 'Status')
                ->render(fn ($activity) => match($activity->status) {
                    'Pendente' => '<span class="badge bg-warning">Pendente</span>',
                    'Concluída' => '<span class="badge bg-success">Concluída</span>',
                    'Cancelada' => '<span class="badge bg-danger">Cancelada</span>',
                }),

            TD::make('user.name', 'Responsável'),

            TD::make('Ações')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function ($activity) {
                    return view('platform.opportunity.activity-actions', compact('activity'));
                }),
        ];
    }
}