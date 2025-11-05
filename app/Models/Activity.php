<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class Activity extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'opportunity_id',
        'user_id',
        'tipo', // Ex: Ligação, Visita, Email, Reunião
        'data_agendada',
        'titulo',
        'descricao',
        'status', // Ex: Pendente, Concluída, Cancelada
    ];
    
    protected $casts = [
        'data_agendada' => 'datetime',
    ];

    /**
     * A atividade pertence a uma Oportunidade.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * A atividade foi criada por um Usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}