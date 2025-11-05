<?php

namespace App\Orchid\Screens;

use App\Models\Vendedor as VendedorModel; 
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class VendedorEditScreen extends Screen
{
    /**
     * @var VendedorModel|null
     */
    public $vendedor;
    
    public function query(VendedorModel $vendedor): array
    {
        $this->vendedor = $vendedor;
        
        $this->name = $vendedor->exists ? 'Editar Vendedor: ' . $vendedor->nome_razao_social : 'Cadastrar Novo Vendedor';
        $this->description = 'Dados do promitente vendedor para fins de contrato.';

        return [
            'vendedor' => $vendedor,
        ];
    }

    public function commandBar(): array
    {
        return [
            \Orchid\Screen\Actions\Button::make('Salvar')->icon('bs.check-circle')->method('save'),
            \Orchid\Screen\Actions\Button::make('Excluir')->icon('bs.trash3')
                ->confirm(__('Tem certeza que deseja excluir o vendedor?'))
                ->method('remove')
                ->canSee($this->vendedor->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Layout::legend('Identificação e Contato'),
                
                Group::make([
                    Input::make('vendedor.nome_razao_social')->title('Nome / Razão Social')->required()->col(6),
                    Input::make('vendedor.cpf_cnpj')->title('CPF / CNPJ')->mask('99.999.999/9999-99')->required()->col(6),
                ]),
                Input::make('vendedor.responsavel')->title('Nome do Responsável (se PJ)'),
                TextArea::make('vendedor.endereco_completo')->title('Endereço Completo')->rows(3),
                Group::make([
                    Input::make('vendedor.telefone')->title('Telefone')->mask('(99) 99999-9999')->col(6),
                    Input::make('vendedor.email')->title('E-mail')->type('email')->col(6),
                ]),
                Layout::legend('Dados Financeiros'),
                Input::make('vendedor.chave_pix')->title('Chave PIX (Para Sinal)'),
                TextArea::make('vendedor.dados_bancarios')->title('Dados Bancários')->rows(2),
            ])->title('Formulário de Vendedor'),
        ];
    }

    /**
     * @param VendedorModel $vendedor 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(VendedorModel $vendedor, Request $request)
    {
        $request->validate([
            'vendedor.nome_razao_social' => 'required',
            'vendedor.cpf_cnpj' => 'required|unique:vendedors,cpf_cnpj,' . $vendedor->id,
        ]);
        
        $vendedor->fill($request->get('vendedor'))->save();

        Toast::info('Vendedor salvo com sucesso!');

        // Redireciona para a lista de vendedores
        return redirect()->route('platform.vendedores.list');
    }

    /**
     * @param VendedorModel $vendedor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(VendedorModel $vendedor)
    {
        if ($vendedor->imoveis()->exists()) {
            Toast::error('Não é possível excluir o vendedor, pois ele possui imóveis vinculados.');
            return redirect()->route('platform.vendedores.list');
        }

        $vendedor->delete();
        Toast::success('Vendedor excluído com sucesso!');

        return redirect()->route('platform.vendedores.list');
    }
}