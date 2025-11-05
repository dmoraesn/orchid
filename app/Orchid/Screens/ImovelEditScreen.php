<?php

namespace App\Orchid\Screens;

use App\Models\Imovel as ImovelModel;
use App\Models\Vendedor as VendedorModel;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Number;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ImovelEditScreen extends Screen
{
    /**
     * @var ImovelModel|null
     */
    public $imovel;
    
    public function query(ImovelModel $imovel): array
    {
        $this->imovel = $imovel;
        
        $this->name = $imovel->exists ? 'Editar Imóvel: ' . $imovel->titulo : 'Cadastrar Novo Imóvel';
        $this->description = 'Detalhes e características da propriedade.';
        
        return [
            'imovel' => $imovel,
        ];
    }

    public function commandBar(): array
    {
        return [
            \Orchid\Screen\Actions\Button::make('Salvar')
                ->icon('bs.check-circle')
                ->method('save'),

            \Orchid\Screen\Actions\Button::make('Excluir')
                ->icon('bs.trash3')
                ->confirm(__('Tem certeza que deseja excluir este imóvel?'))
                ->method('remove')
                ->canSee($this->imovel->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Layout::legend('Dados Essenciais'),
                
                Group::make([
                    Select::make('imovel.vendedor_id')
                        ->fromModel(VendedorModel::class, 'nome_razao_social')
                        ->title('Vendedor / Construtora')
                        ->required()
                        ->empty('Selecione um Vendedor...'),

                    Select::make('imovel.tipo_imovel')
                        ->options(['Apartamento' => 'Apartamento', 'Casa' => 'Casa', 'Terreno' => 'Terreno', 'Comercial' => 'Comercial'])
                        ->title('Tipo de Imóvel')
                        ->required()
                        ->empty('Selecione o Tipo'),
                ]),
                
                Input::make('imovel.titulo')
                    ->title('Título do Anúncio')
                    ->required()
                    ->placeholder('Ex: Apartamento 4 Qts no Meireles'),

                Layout::legend('Valores e Status'),
                
                Group::make([
                    Input::make('imovel.valor_venda')
                        ->title('Valor de Venda (R$)')
                        ->type('number')
                        ->required()
                        ->mask(['alias' => 'currency', 'prefix' => 'R$ ', 'groupSeparator' => '.', 'radixPoint' => ',', 'digits' => 2])
                        ->placeholder('6500000.00')->col(6),

                    Select::make('imovel.status_venda')
                        ->options(['Disponível' => 'Disponível', 'Reservado' => 'Reservado', 'Vendido' => 'Vendido'])
                        ->title('Status de Venda')->required()->default('Disponível')->col(6),
                ]),

                TextArea::make('imovel.endereco_completo')
                    ->title('Endereço Completo')
                    ->rows(2)
                    ->required()
                    ->placeholder('Avenida Antônio Justa, 3000'),

                Layout::legend('Características e Documentação'),

                Group::make([
                    Number::make('imovel.area_privativa')->title('Área Privativa (m²)')->step(0.01)->required()->placeholder('364')->col(3),
                    Number::make('imovel.quartos')->title('Quartos')->placeholder('4')->col(3),
                    Number::make('imovel.banheiros')->title('Banheiros')->placeholder('5')->col(3),
                    Number::make('imovel.vagas_garagem')->title('Vagas de Garagem')->placeholder('5')->col(3),
                ]),
                
                Input::make('imovel.matricula_rip')->title('Matrícula / RIP')->placeholder('123.456'),
                
                Quill::make('imovel.descricao_completa')->title('Descrição Completa')->placeholder('Inclua detalhes do condomínio, lazer e vizinhança.'),
                
            ])->title('Formulário de Imóvel'),
        ];
    }

    /**
     * @param ImovelModel $imovel
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(ImovelModel $imovel, Request $request)
    {
        $request->validate([
            'imovel.titulo' => 'required|max:255',
            'imovel.vendedor_id' => 'required|exists:vendedors,id',
            'imovel.valor_venda' => 'required|numeric|min:0',
        ]);
        
        $imovel->fill($request->get('imovel'))->save();

        Toast::info('Imóvel salvo com sucesso!');

        // Redireciona para a lista (que é a ImovelListScreen)
        return redirect()->route('platform.imoveis.list');
    }

    /**
     * @param ImovelModel $imovel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(ImovelModel $imovel)
    {
        $imovel->delete();

        Toast::success('Imóvel excluído com sucesso!');

        return redirect()->route('platform.imoveis.list');
    }
}