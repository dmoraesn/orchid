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
    /**
     * Nome e descrição da tela
     */
    public string $name = 'Kanban de Oportunidades';
    public string $description = 'Visão Kanban do pipeline de vendas.';

    /**
     * Etapas do pipeline
     */
    protected array $stages = [
        'Novo Lead / Sem Atendimento',
        'Qualificação / Em Atendimento',
        'Apresentação / Visita',
        'Proposta / Negociação',
        'Formalização (Arras)',
        'Fechado Ganho',
        'Perdido (Lost)',
    ];

    /**
     * Dados compartilhados com o layout
     */
    protected array $data = [];

    /**
     * Botões do topo
     */
    public function commandBar(): array
    {
        return [
            Link::make('Nova Oportunidade')
                ->icon('plus')
                ->route('platform.opportunity.create'),
        ];
    }

    /**
     * Consulta principal
     */
    public function query(): array
    {
        $user = auth()->user();

        $filters = request()->only(['nome_lead', 'email', 'etapa_pipeline', 'corretor_id']);

        $opportunities = Opportunity::query()
            ->when(!$user->hasAccess('platform.systems'), fn($q) =>
                $q->where('corretor_id', $user->id)
            )
            ->when($filters['nome_lead'] ?? false, fn($q, $v) =>
                $q->where('nome_lead', 'like', "%$v%")
            )
            ->when($filters['email'] ?? false, fn($q, $v) =>
                $q->where('email', 'like', "%$v%")
            )
            ->when($filters['etapa_pipeline'] ?? false, fn($q, $v) =>
                $q->where('etapa_pipeline', $v)
            )
            ->when($filters['corretor_id'] ?? false, fn($q, $v) =>
                $q->where('corretor_id', $v)
            )
            ->get();

        // Agrupa as oportunidades por etapa
        $kanbanData = collect($this->stages)
            ->mapWithKeys(fn($stage) => [
                $stage => $opportunities->where('etapa_pipeline', $stage)
            ]);

        // Lista de corretores disponíveis
        $corretores = User::query()
            ->whereHas('roles', fn($q) => $q->whereIn('slug', [
                'corretor', 'imobiliaria', 'administrator'
            ]))
            ->pluck('name', 'id')
            ->toArray();

        return $this->data = [
            'stages'     => $this->stages,
            'kanbanData' => $kanbanData,
            'corretores' => $corretores,
            'filters'    => $filters,
        ];
    }

    /**
     * Layout principal da tela (Filtros + Kanban)
     */
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
            ])
            ->title('Filtros')
            ->canSee(true), // depois podemos esconder atrás de um botão “Mostrar Filtros”

            KanbanLayout::class,
        ];
    }

    /**
     * Atualiza o estágio da oportunidade via drag-and-drop
     */
    public function updateStage(Request $request)
    {
        $validated = $request->validate([
            'opportunity_id' => ['required', 'exists:opportunities,id'],
            'new_stage'      => ['required', 'in:' . implode(',', $this->stages)],
        ]);

        $opportunity = Opportunity::findOrFail($validated['opportunity_id']);

        if (!auth()->user()->hasAccess('platform.systems') && $opportunity->corretor_id !== auth()->id()) {
            Toast::error('Você não tem permissão para mover esta oportunidade.');
            return response()->json(['success' => false], 403);
        }

        $opportunity->update(['etapa_pipeline' => $validated['new_stage']]);

        Toast::success("Oportunidade #{$opportunity->id} movida para '{$validated['new_stage']}'.");
        return response()->json(['success' => true]);
    }
}
