<?php

namespace App\Orchid\Screens;

use App\Models\Opportunity;
use App\Models\User;
use App\Orchid\Layouts\Kanban\KanbanLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class OpportunityListScreen extends Screen
{
    protected array $stages = [
        'Novo Lead / Sem Atendimento',
        'Qualificação / Em Atendimento',
        'Apresentação / Visita',
        'Proposta / Negociação',
        'Formalização (Arras)',
        'Fechado Ganho',
        'Perdido (Lost)',
    ];

    // Propriedades públicas para uso no layout()
    public array $data = [];

    public $name = 'Kanban de Oportunidades';
    public $description = 'Visão Kanban do pipeline de vendas.';

    public function commandBar(): array
    {
        return [
            Link::make('Nova Oportunidade')
                ->icon('plus')
                ->route('platform.opportunity.create'),
        ];
    }

    public function query(): array
    {
        $user = auth()->user();

        $opportunities = Opportunity::query()
            ->when(! $user->hasAccess('platform.systems'), function ($q) use ($user) {
                $q->where('corretor_id', $user->id);
            })
            ->when(request('nome_lead'), fn($q, $v) => $q->where('nome_lead', 'like', "%$v%"))
            ->when(request('email'), fn($q, $v) => $q->where('email', 'like', "%$v%"))
            ->when(request('etapa_pipeline'), fn($q, $v) => $q->where('etapa_pipeline', $v))
            ->when(request('corretor_id'), fn($q, $v) => $q->where('corretor_id', $v))
            ->get();

        $kanbanData = $opportunities->groupBy('etapa_pipeline');
        $kanbanData = collect($this->stages)->mapWithKeys(fn($s) => [$s => $kanbanData->get($s, collect())]);

        $corretores = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['corretor', 'imobiliaria', 'administrator']))
            ->pluck('name', 'id')
            ->toArray();

        $filters = request()->only(['nome_lead', 'email', 'etapa_pipeline', 'corretor_id']);

        // Armazena os dados na propriedade pública
        $this->data = [
            'stages'     => $this->stages,
            'kanbanData' => $kanbanData,
            'corretores' => $corretores,
            'filters'    => $filters,
        ];

        return $this->data;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('nome_lead')
                    ->title('Nome do Lead')
                    ->placeholder('Buscar...')
                    ->value($this->data['filters']['nome_lead'] ?? ''),

                Input::make('email')
                    ->title('E-mail')
                    ->type('email')
                    ->value($this->data['filters']['email'] ?? ''),

                Select::make('etapa_pipeline')
                    ->title('Etapa')
                    ->options(array_combine($this->data['stages'], $this->data['stages']))
                    ->empty('Todas as etapas')
                    ->value($this->data['filters']['etapa_pipeline'] ?? ''),

                Select::make('corretor_id')
                    ->title('Corretor')
                    ->options($this->data['corretores'])
                    ->empty('Todos os corretores')
                    ->value($this->data['filters']['corretor_id'] ?? ''),
            ])->title('Filtros'),

            KanbanLayout::class,
        ];
    }

    public function updateStage(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required|exists:opportunities,id',
            'new_stage'      => 'required|in:' . implode(',', $this->stages),
        ]);

        $opportunity = Opportunity::findOrFail($request->input('opportunity_id'));
        $newStage    = $request->input('new_stage');

        if (! auth()->user()->hasAccess('platform.systems') && $opportunity->corretor_id !== auth()->id()) {
            Toast::error('Você não tem permissão para mover esta oportunidade.');
            return response()->json(['success' => false], 403);
        }

        $opportunity->etapa_pipeline = $newStage;
        $opportunity->save();

        Toast::success("Oportunidade #{$opportunity->id} movida para '{$newStage}'.");
        return response()->json(['success' => true]);
    }
}