<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function test()
    {
        // Pega todos os leads com seus corretores
        $leads = Lead::with('user')->get();

        // Mostra estrutura para debug
        return response()->json([
            'total' => $leads->count(),
            'data' => $leads,
        ]);
    }
}
