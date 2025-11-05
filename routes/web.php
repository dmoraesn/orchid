<?php

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;

Route::get('/leads/test', [LeadController::class, 'test']);

Route::post('/kanban/move-lead', function (Request $request) {
    $lead = App\Models\Lead::findOrFail($request->lead_id);
    $lead->stage_id = $request->stage_id;
    $lead->save();

    return response()->json(['success' => true]);
});

