<?php

namespace App\Orchid\Screens;

use App\Models\Opportunity;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class DashboardScreen extends Screen
{
    public $name = 'Dashboard CRM';
    public $description = 'Visão geral do pipeline e atividades';

    public function query(): array
    {
        $user = Auth::user();
        $isAdmin = $user->hasAccess('platform.systems');

        $opportunities = Opportunity::query()
            ->when(!$isAdmin, fn($q) => $q->where('corretor_id', $user->id))
            ->get();

        $stages = [
            'Novo Lead / Sem Atendimento',
            'Qualificação / Em Atendimento',
            'Apresentação / Visita',
            'Proposta / Negociação',
            'Formalização (Arras)',
            'Fechado Ganho',
            'Perdido (Lost)',
        ];

        $pipeline = collect($stages)->mapWithKeys(function ($stage) use ($opportunities) {
            return [$stage => $opportunities->where('etapa_pipeline', $stage)->count()];
        });

        $totalValue = $opportunities->whereNotIn('etapa_pipeline', ['Fechado Ganho', 'Perdido (Lost)'])
            ->sum('valor_max_compra');

        $wonValue = $opportunities->where('etapa_pipeline', 'Fechado Ganho')->sum('valor_max_compra');

        $pendingActivities = Activity::where('status', 'Pendente')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->with('opportunity')
            ->latest('data_agendada')
            ->limit(5)
            ->get();

        return [
            'pipeline' => $pipeline,
            'totalLeads' => $opportunities->count(),
            'totalValue' => $totalValue,
            'wonValue' => $wonValue,
            'pendingActivities' => $pendingActivities,
            'stages' => $stages,
        ];
    }

    public function layout(): array
    {
        // CORREÇÃO: Use $this->query() em vez de $this->query
        $data = $this->query();

        return [
            Layout::columns([
                // KPIs
                Layout::view('platform.dashboard.kpis', [
                    'totalLeads' => $data['totalLeads'],
                    'totalValue' => $data['totalValue'],
                    'wonValue' => $data['wonValue'],
                ]),

                // Funil
                Layout::view('platform.dashboard.pipeline', [
                    'pipeline' => $data['pipeline'],
                    'stages' => $data['stages'],
                ]),
            ]),

            // Atividades Pendentes
            Layout::table('pendingActivities', [
                TD::make('opportunity.nome_lead', 'Lead')
                    ->render(fn($activity) => $activity->opportunity?->nome_lead ?? '—'),

                TD::make('titulo', 'Atividade'),

                TD::make('data_agendada', 'Data')
                    ->render(fn($activity) => $activity->data_agendada->format('d/m H:i')),

                TD::make('Ações')
                    ->render(fn($activity) => view('platform.dashboard.activity-action', compact('activity'))),
            ])->title('Atividades Pendentes'),
        ];
    }
}