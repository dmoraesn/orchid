<?php

namespace App\Orchid\Screens;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OpportunityListScreen extends Screen
{
    /** Título da página */
    public $name = 'Kanban de Oportunidades';

    /** Descrição */
    public $description = 'Visualização Kanban do funil de vendas com arrastar e soltar.';

    /** Permissão necessária */
    public $permission = ['platform.opportunity.list'];

    /**
     * Dados para a view.
     */
    public function query(): array
    {
        $filters = request()->get('filters', []);

        return [
            'stages'     => $this->stages(),
            'kanbanData' => $this->getKanbanData($filters),
            'users'      => User::select('id', 'name')->get(),
            'filters'    => $filters, // Para manter estado no modal
        ];
    }

    /**
     * Barra de comandos (botões superiores).
     */
    public function commandBar(): array
    {
        return [
            ModalToggle::make('Filtros')
                ->modal('filterModal')
                ->icon('filter')
                ->class('btn btn-sm btn-outline-primary'),

            Link::make('Nova Oportunidade')
                ->icon('plus')
                ->route('platform.opportunity.create')
                ->class('btn btn-sm btn-primary'),
        ];
    }

    /**
     * Layout da tela.
     */
    public function layout(): array
    {
        return [
            // Modal de filtros
            Layout::modal('filterModal', [
                Layout::rows([
                    Input::make('filters.name')
                        ->title('Nome do Lead')
                        ->placeholder('Buscar por nome...')
                        ->value(request('filters.name')),

                    Input::make('filters.email')
                        ->title('E-mail')
                        ->placeholder('Buscar por e-mail...')
                        ->value(request('filters.email')),

                    Select::make('filters.etapa')
                        ->title('Etapa')
                        ->options(array_combine($this->stages(), $this->stages()))
                        ->empty('Todas as etapas')
                        ->value(request('filters.etapa')),

                    Select::make('filters.corretor')
                        ->title('Corretor')
                        ->fromModel(User::class, 'name', 'id')
                        ->empty('Todos os corretores')
                        ->value(request('filters.corretor')),
                ]),
            ])
                ->title('Filtros')
                ->applyButton('Aplicar')
                ->closeButton('Fechar'),

            // Kanban principal
            Layout::view('platform.kanban.kanban'),
        ];
    }

    /**
     * Etapas fixas do pipeline.
     */
    protected function stages(): array
    {
        return [
            'Novo Lead / Sem Atendimento',
            'Qualificação / Em Atendimento',
            'Apresentação / Visita',
            'Proposta / Negociação',
            'Formalização (Arras)',
            'Fechado Ganho',
            'Perdido (Lost)',
        ];
    }

    /**
     * Dados agrupados por etapa, com suporte a filtros.
     */
    protected function getKanbanData(array $filters = []): array
    {
        $data = [];

        foreach ($this->stages() as $stage) {
            $query = Lead::where('etapa', $stage);

            if (!empty($filters['name'])) {
                $query->where('nome', 'like', '%' . $filters['name'] . '%');
            }

            if (!empty($filters['email'])) {
                $query->where('email', 'like', '%' . $filters['email'] . '%');
            }

            if (!empty($filters['corretor'])) {
                $query->where('user_id', $filters['corretor']);
            }

            if (!empty($filters['etapa']) && $filters['etapa'] !== $stage) {
                continue; // Pula etapas que não batem com o filtro
            }

            $data[$stage] = $query->take(5)->get();
        }

        return $data;
    }

    /**
     * Atualiza a etapa do lead via drag & drop (AJAX).
     */
    public function updateStage(Request $request)
    {
        $validated = $request->validate([
            'opportunity_id' => 'required|integer|exists:leads,id',
            'new_stage'      => 'required|string|in:' . implode(',', $this->stages()),
        ]);

        $lead = Lead::findOrFail($validated['opportunity_id']);
        $lead->etapa = $validated['new_stage'];
        $lead->save();

        Toast::success('Etapa atualizada com sucesso!');

        return response()->json(['success' => true]);
    }

    /**
     * Carrega mais leads via AJAX (botão "Ver mais").
     */
    public function loadMore(Request $request)
    {
        $request->validate([
            'etapa'  => 'required|string|in:' . implode(',', $this->stages()),
            'offset' => 'required|integer|min:0',
        ]);

        $etapa  = $request->etapa;
        $offset = $request->integer('offset');
        $limit  = 5;

        $filters = $request->get('filters', []);
        $query   = Lead::where('etapa', $etapa);

        if (!empty($filters['name'])) {
            $query->where('nome', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        if (!empty($filters['corretor'])) {
            $query->where('user_id', $filters['corretor']);
        }

        $total = $query->count();
        $leads = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'leads'   => $leads->map(fn($l) => [
                'id'    => $l->id,
                'nome'  => $l->nome ?? 'Sem nome',
                'email' => $l->email ?? 'Sem e-mail',
                'valor' => $l->valor ?? 0,
            ]),
            'hasMore' => ($offset + $limit) < $total,
        ]);
    }
}
