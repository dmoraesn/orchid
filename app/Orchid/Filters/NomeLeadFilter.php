<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class NomeLeadFilter extends Filter
{
    public function name(): string { return 'nome_lead'; }

    public function run(Builder $builder): Builder
    {
        return $builder->where('nome_lead', 'like', '%' . $this->request->get('nome_lead') . '%');
    }

    public function display(): array
    {
        return [
            Input::make('nome_lead')
                ->title('Nome do Lead')
                ->placeholder('Buscar nome...')
                ->value($this->request->get('nome_lead')),
        ];
    }
}