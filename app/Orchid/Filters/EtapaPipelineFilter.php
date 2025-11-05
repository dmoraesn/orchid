<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class EtapaPipelineFilter extends Filter
{
    protected $stages = [
        'Novo Lead / Sem Atendimento',
        'Qualificação / Em Atendimento',
        'Apresentação / Visita',
        'Proposta / Negociação',
        'Formalização (Arras)',
        'Fechado Ganho',
        'Perdido (Lost)',
    ];

    public function name(): string { return 'etapa_pipeline'; }

    public function run(Builder $builder): Builder
    {
        return $this->request->filled('etapa_pipeline')
            ? $builder->where('etapa_pipeline', $this->request->get('etapa_pipeline'))
            : $builder;
    }

    public function display(): array
    {
        return [
            Select::make('etapa_pipeline')
                ->title('Etapa')
                ->options(array_combine($this->stages, $this->stages))
                ->empty('Todas as etapas')
                ->value($this->request->get('etapa_pipeline')),
        ];
    }
}