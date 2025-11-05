<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class EmailFilter extends Filter
{
    public function name(): string { return 'email'; }

    public function run(Builder $builder): Builder
    {
        return $builder->where('email', 'like', '%' . $this->request->get('email') . '%');
    }

    public function display(): array
    {
        return [
            Input::make('email')
                ->title('E-mail')
                ->placeholder('ex: joao@...')
                ->value($this->request->get('email')),
        ];
    }
}