<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    /**
     * Dados para a tela.
     */
    public function query(Request $request): iterable
    {
        return [
            'user' => $request->user(),
        ];
    }

    /**
     * Título da tela.
     */
    public function name(): ?string
    {
        return 'Minha Conta';
    }

    /**
     * Descrição.
     */
    public function description(): ?string
    {
        return 'Atualize suas informações de perfil, como nome, e-mail e senha.';
    }

    /**
     * Botões de ação.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Voltar para minha conta')
                ->novalidate()
                ->canSee(Impersonation::isSwitch())
                ->icon('bs.people')
                ->route('platform.switch.logout'),

            Button::make('Sair')
                ->novalidate()
                ->icon('bs.box-arrow-left')
                ->route('platform.logout'),
        ];
    }

    /**
     * Layout da tela.
     */
    public function layout(): iterable
    {
        return [
            Layout::block(UserEditLayout::class)
                ->title('Informações do Perfil')
                ->description('Atualize as informações do seu perfil e endereço de e-mail.')
                ->commands(
                    Button::make('Salvar')
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title('Atualizar Senha')
                ->description('Garanta que sua conta use uma senha longa e aleatória para maior segurança.')
                ->commands(
                    Button::make('Atualizar senha')
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('changePassword')
                ),
        ];
    }

    /**
     * MÉTODO OBRIGATÓRIO – Orchid exige ...$arguments
     */
    public function __invoke(Request $request, ...$arguments): iterable
    {
        return $this->query($request);
    }

    /**
     * Salvar perfil.
     */
    public function save(Request $request): void
    {
        $user = $request->user();

        $request->validate([
            'user.name'  => 'required|string|max:255',
            'user.email' => [
                'required',
                'email',
                Rule::unique(User::class, 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($request->get('user'))->save();

        Toast::success('Perfil atualizado com sucesso.');
    }

    /**
     * Alterar senha.
     */
    public function changePassword(Request $request): void
    {
        $request->validate([
            'old_password'          => 'required|current_password:web',
            'password'              => 'required|confirmed|min:8|different:old_password',
            'password_confirmation' => 'required',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        Toast::success('Senha alterada com sucesso.');
    }
}
