<?php

namespace App\Orchid\Screens;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OpportunityCreateScreen extends Screen
{
    public $name = 'Nova Oportunidade';
    public $description = 'Criar um novo lead no pipeline';

    public function query(): array
    {
        return [
            'corretores' => User::whereHas('roles', fn($q) => $q->whereIn('slug', ['corretor', 'imobiliaria', 'administrator']))
                ->pluck('name', 'id')
                ->toArray(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Salvar')
                ->icon('check')
                ->method('create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('nome_lead')
                    ->title('Nome do Lead')
                    ->placeholder('Ex: João Silva')
                    ->required(),

                Input::make('telefone')
                    ->title('Telefone')
                    ->mask('(99) 99999-9999')
                    ->placeholder('(11) 98765-4321'),

                Input::make('email')
                    ->title('E-mail')
                    ->type('email')
                    ->placeholder('joao@exemplo.com'),

                Select::make('fonte_lead')
                    ->title('Fonte do Lead')
                    ->options([
                        'Site' => 'Site',
                        'Indicação' => 'Indicação',
                        'Anúncio' => 'Anúncio',
                        'Redes Sociais' => 'Redes Sociais',
                        'Outro' => 'Outro',
                    ])
                    ->empty('Selecione...'),

                Select::make('corretor_id')
                    ->title('Corretor Responsável')
                    ->fromModel(User::class, 'name')
                    ->whereHas('roles', fn($q) => $q->whereIn('slug', ['corretor', 'imobiliaria', 'administrator']))
                    ->required(),

                Select::make('etapa_pipeline')
                    ->title('Etapa Inicial')
                    ->options([
                        'Novo Lead / Sem Atendimento' => 'Novo Lead / Sem Atendimento',
                        'Qualificação / Em Atendimento' => 'Qualificação / Em Atendimento',
                    ])
                    ->value('Novo Lead / Sem Atendimento'),

                Input::make('valor_max_compra')
                    ->title('Valor Máximo de Compra')
                    ->mask('currency')
                    ->placeholder('R$ 500.000,00'),

                TextArea::make('preferencia_imovel')
                    ->title('Preferência do Imóvel')
                    ->rows(3)
                    ->placeholder('Ex: 3 quartos, centro, até 80m²...'),
            ]),
        ];
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'nome_lead' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'fonte_lead' => 'nullable|string|max:100',
            'corretor_id' => 'required|exists:users,id',
            'etapa_pipeline' => 'required|string',
            'valor_max_compra' => 'nullable|numeric|min:0',
            'preferencia_imovel' => 'nullable|string',
        ]);

        // Limpa máscara do valor
        if ($data['valor_max_compra']) {
            $data['valor_max_compra'] = str_replace(['R$', ' ', '.'], '', $data['valor_max_compra']);
            $data['valor_max_compra'] = str_replace(',', '.', $data['valor_max_compra']);
        }

        Opportunity::create($data);

        Toast::success('Oportunidade criada com sucesso!');

        return redirect()->route('platform.opportunity.list');
    }
}