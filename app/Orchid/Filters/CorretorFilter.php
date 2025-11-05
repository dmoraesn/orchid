<?php

namespace App\Orchid\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class CorretorFilter extends Filter
{
    public function name(): string { return 'corretor_id'; }

    public function run(Builder $builder): Builder
    {
        return $this->request->filled('corretor_id')
            ? $builder->where('corretor_id', $this->request->get('corretor_id'))
            : $builder;
    }

    public function display(): array
    {
        $corretores = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['corretor', 'imobiliaria', 'administrator']))
            ->pluck('name', 'id');

        return [
            Select::make('corretor_id')
                ->title('Corretor')
                ->options($corretores)
                ->empty('Todos os corretores')
                ->value($this->request->get('corretor_id')),
        ];
    }
}