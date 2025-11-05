<?php

namespace App\Orchid\Layouts\Kanban;

use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

class KanbanLayout extends Layout
{
    protected $template = 'platform.kanban.kanban'; // ajuste se necessário

    /**
     * Renderiza o Kanban
     */
    public function build(Repository $repository)
    {
        $stages = $repository->get('stages');
        $kanbanData = $repository->get('kanbanData');

        return view($this->template, compact('stages', 'kanbanData'));
    }
}