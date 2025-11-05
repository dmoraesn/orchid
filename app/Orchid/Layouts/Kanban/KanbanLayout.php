<?php

namespace App\Orchid\Layouts\Kanban;

use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

/**
 * Layout responsável por renderizar o Kanban de Oportunidades.
 *
 * Este layout usa um template Blade localizado em:
 * resources/views/platform/kanban/kanban.blade.php
 */
class KanbanLayout extends Layout
{
    /**
     * Renderiza o layout Kanban personalizado.
     *
     * @param Repository $repository
     * @return \Illuminate\Contracts\View\View
     */
    public function build(Repository $repository)
    {
        // Recupera dados passados pelo Screen
        $stages = $repository->get('stages', []);
        $kanbanData = $repository->get('kanbanData', collect());

        // Renderiza a view do Kanban
        return view('platform.kanban.kanban', [
            'stages'     => $stages,
            'kanbanData' => $kanbanData,
        ]);
    }
}
