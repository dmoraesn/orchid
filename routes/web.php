<?php

use App\Http\Controllers\LeadController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kanban & Lead Routes
|--------------------------------------------------------------------------
|
| Rotas relacionadas ao Kanban de Oportunidades e carregamento dinâmico.
|
*/

// ---------------------------------------------------------------------
// 1. Rota de teste (opcional - remova em produção)
Route::get('/leads/test', [LeadController::class, 'test'])
    ->name('leads.test');

// ---------------------------------------------------------------------
// 2. Atualizar etapa do lead via drag & drop (AJAX)
Route::post('/kanban/move-lead', function (Request $request) {
    $request->validate([
        'lead_id'   => 'required|integer|exists:leads,id',
        'stage_id'  => 'required|integer|exists:stages,id', // ajuste conforme sua tabela
    ]);

    $lead = Lead::findOrFail($request->lead_id);
    $lead->stage_id = $request->stage_id;
    $lead->save();

    return response()->json(['success' => true]);
})->name('kanban.move-lead');

// ---------------------------------------------------------------------
// 3. Carregar mais leads (botão "Ver mais")
Route::get('/oportunidades/load-more', function (Request $request) {
    $request->validate([
        'etapa'  => 'required|string|max:50',
        'offset' => 'integer|min:0',
    ]);

    $etapa  = $request->get('etapa');
    $offset = (int) $request->get('offset', 0);
    $limit  = 5;

    $query = Lead::where('etapa', $etapa);
    $total = $query->count();
    $leads = $query->skip($offset)->take($limit)->get();

    return response()->json([
        'leads'   => $leads->map(fn($lead) => [
            'id'     => $lead->id,
            'nome'   => $lead->nome ?? 'Sem nome',
            'email'  => $lead->email ?? 'Sem e-mail',
            'valor'  => $lead->valor ?? 0,
        ]),
        'hasMore' => ($offset + $limit) < $total,
    ]);
})->name('platform.opportunity.load_more');
