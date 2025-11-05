<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\User;
use App\Orchid\Layouts\Activity\ActivityEditLayout;
use App\Orchid\Layouts\Activity\ActivityListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Validation\Rule;

class OpportunityEditScreen extends Screen
{
    /**
     * @var Opportunity
     */
    public $opportunity;

    /**
     * The name of the screen.
     */
    public $name = 'Detalhes da Oportunidade';

    /**
     * Fetch data for the screen.
     */
    public function query(Opportunity $opportunity, Activity $activity): array
    {
        $this->opportunity = $opportunity;
        
        $this->name = $opportunity->exists 
            ? 'Editar Oportunidade: ' . $opportunity->nome_lead 
            : 'Criar Nova Oportunidade';

        return [
            'opportunity' => $opportunity,
            // Adiciona as atividades para o Layout de Tabela (MVP 1.4)
            'activities'  => $opportunity->activities()->orderBy('data_agendada', 'desc')->get(),
            // Adiciona a atividade (vazia ou para edição) para o Modal
            'activity'    => $activity, 
        ];
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): array
    {
        return [
            Button::make('Salvar Oportunidade')
                ->icon('bs.check-circle')
                ->method('createOrUpdate')
                ->canSee(!$this->opportunity->exists),

            Button::make('Atualizar Oportunidade')
                ->icon('bs.pencil')
                ->method('createOrUpdate')
                ->canSee($this->opportunity->exists),

            Button::make('Excluir Oportunidade')
                ->icon('bs.trash3')
                ->confirm('Tem certeza que deseja excluir esta Oportunidade e todas as suas Atividades?')
                ->method('remove')
                ->canSee($this->opportunity->exists),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): array
    {
        return [
            Layout::tabs([
                'Dados do Lead' => [
                    Layout::rows([
                        Input::make('opportunity.nome_lead')
                            ->title('Nome do Lead')
                            ->placeholder('Nome Completo do Lead')
                            ->required(),
                        
                        Relation::make('opportunity.corretor_id')
                            ->title('Corretor Responsável')
                            ->placeholder('Selecione um Corretor')
                            ->required()
                            ->fromModel(User::class, 'name'), 

                        Input::make('opportunity.telefone')
                            ->title('Telefone')
                            ->mask('(99) 99999-9999'),
                        
                        Input::make('opportunity.email')
                            ->title('Email')
                            ->type('email'),
                            
                        Select::make('opportunity.fonte_lead')
                            ->title('Fonte do Lead')
                            ->options([
                                'Site'    => 'Site',
                                'Telefone' => 'Telefone',
                                'Indicação' => 'Indicação',
                                'Parceiro' => 'Parceiro',
                                'Outro'   => 'Outro',
                            ])
                            ->required(),
                            
                        Select::make('opportunity.etapa_pipeline')
                            ->title('Etapa do Pipeline')
                            ->options([
                                'Novo Lead / Sem Atendimento'   => 'Novo Lead / Sem Atendimento',
                                'Qualificação / Em Atendimento' => 'Qualificação / Em Atendimento',
                                'Apresentação / Visita'         => 'Apresentação / Visita',
                                'Proposta / Negociação'         => 'Proposta / Negociação',
                                'Formalização (Arras)'          => 'Formalização (Arras)',
                                'Fechado Ganho'                 => 'Fechado Ganho',
                                'Perdido (Lost)'                => 'Perdido (Lost)',
                            ])
                            ->required(),
                        
                        TextArea::make('opportunity.preferencia_imovel')
                            ->title('Preferência do Imóvel')
                            ->placeholder('Ex: 2 quartos, até R$ 500.000, na região X.')
                            ->rows(3),

                        Input::make('opportunity.valor_max_compra')
                            ->title('Valor Máximo de Compra (R$)')
                            ->type('number')
                            ->placeholder('500000.00'),

                        Input::make('opportunity.documento_numero')->title('CPF/CNPJ'),
                        Input::make('opportunity.data_nascimento')->title('Data de Nascimento')->type('date'),
                        Input::make('opportunity.estado_civil')->title('Estado Civil'),
                        Input::make('opportunity.profissao')->title('Profissão'),
                        TextArea::make('opportunity.endereco_cliente')->title('Endereço Completo'),
                        
                    ])->title('Informações da Oportunidade'),
                ],
                
                // MVP 1.4: Tabela e Formulário de Atividades
                'Atividades' => [
                    Layout::block(ActivityListLayout::class)
                        ->title('Histórico de Atividades')
                        ->description('Todas as ligações, visitas e reuniões agendadas para esta oportunidade.')
                        ->commands([
                            ModalToggle::make('Nova Atividade')
                                ->modal('activityModal')
                                ->method('createOrUpdateActivity')
                                ->icon('bs.plus-circle'),
                        ]),
                ],
            ]),

            // MODAL PARA CRIAÇÃO/EDIÇÃO DE ATIVIDADE
            Layout::modal('activityModal', [
                ActivityEditLayout::class
            ])
            ->title('Gerenciar Atividade')
            ->applyButton('Salvar')
            ->async('asyncGetActivity'), // Habilita o carregamento assíncrono para edição
        ];
    }
    
    // =======================================================================
    // MÉTODOS DE PERSISTÊNCIA DA OPORTUNIDADE
    // =======================================================================
    
    /**
     * Salva ou atualiza a Oportunidade.
     */
    public function createOrUpdate(Opportunity $opportunity, Request $request)
    {
        $request->validate([
            'opportunity.nome_lead'   => 'required|string',
            'opportunity.etapa_pipeline' => 'required|string',
            'opportunity.corretor_id' => 'required|exists:users,id',
            'opportunity.valor_max_compra' => 'nullable|numeric|min:0',
        ]);

        $opportunity->fill($request->get('opportunity'))->save();

        Toast::success('Oportunidade salva com sucesso!');

        // Redireciona para a própria tela de edição após criação
        if (!$opportunity->wasRecentlyCreated) {
             return redirect()->route('platform.opportunity.edit', $opportunity);
        }

        return redirect()->route('platform.opportunity.list');
    }

    /**
     * Remove a Oportunidade e suas Atividades.
     */
    public function remove(Opportunity $opportunity)
    {
        $opportunity->activities()->delete(); // Exclui atividades relacionadas
        $opportunity->delete();
        
        Toast::warning('Oportunidade excluída.');

        return redirect()->route('platform.opportunity.list');
    }
    
    // =======================================================================
    // MÉTODOS ASYNC E CRUD DE ATIVIDADE (MVP 1.4)
    // =======================================================================

    /**
     * Carrega os dados da atividade para o modal (usado na edição).
     */
    public function asyncGetActivity(Activity $activity): array
    {
        return [
            'activity' => $activity,
        ];
    }

    /**
     * Cria ou atualiza uma atividade.
     * @param Request $request
     */
    public function createOrUpdateActivity(Opportunity $opportunity, Request $request)
    {
        $validated = $request->validate([
            'activity.tipo'           => ['required', 'string'],
            'activity.titulo'         => ['required', 'string'],
            'activity.data_agendada'  => ['required', 'date'],
            'activity.status'         => ['required', Rule::in(['Pendente', 'Concluída', 'Cancelada'])],
            'activity.descricao'      => ['nullable', 'string'],
            'activity.user_id'        => ['required', 'exists:users,id'],
        ]);
        
        // Determina se é criação (sem ID) ou edição (com ID)
        $activityId = $request->input('activity.id');

        $activity = $activityId 
            ? Activity::findOrFail($activityId)
            : new Activity();
        
        // Associa à oportunidade
        $activity->opportunity_id = $opportunity->id;
        
        // Preenche e salva
        $activity->fill($validated['activity'])->save();
        
        Toast::success('Atividade salva com sucesso!');
        
        // O redirect recarrega a tela para atualizar a tabela de atividades
        return redirect()->route('platform.opportunity.edit', $opportunity);
    }

    /**
     * Remove a atividade.
     */
    public function removeActivity(Opportunity $opportunity, Request $request)
    {
        $activityId = $request->input('activity_id');
        Activity::destroy($activityId);
        
        Toast::warning('Atividade removida.');
        
        return redirect()->route('platform.opportunity.edit', $opportunity);
    }
}