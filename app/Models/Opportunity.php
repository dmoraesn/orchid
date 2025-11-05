<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Orchid Traits
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    /**
     * Tabela no banco de dados
     */
    protected $table = 'opportunities';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'nome_lead',
        'telefone',
        'email',
        'fonte_lead',
        'etapa_pipeline',
        'corretor_id',
        'preferencia_imovel',
        'valor_max_compra',

        // Dados contratuais (opcional)
        'documento_numero',
        'data_nascimento',
        'estado_civil',
        'profissao',
        'endereco_cliente',
    ];

    /**
     * Filtros customizados (OBRIGATÓRIO quando usar Layout::filters())
     * Cada item aqui é uma classe real de filtro.
     */
    protected $filters = [
        \App\Orchid\Filters\NomeLeadFilter::class,
        \App\Orchid\Filters\EmailFilter::class,
        \App\Orchid\Filters\EtapaPipelineFilter::class,
        \App\Orchid\Filters\CorretorFilter::class,
    ];

    /**
     * Filtros simples via URL (ex: ?corretor_id=5)
     * DEIXE VAZIO se estiver usando filtros visuais!
     */
    protected $allowedFilters = [];

    /**
     * Ordenação permitida via URL (ex: ?sort=-updated_at)
     */
    protected $allowedSorts = [
        'nome_lead',
        'updated_at',
        'valor_max_compra',
    ];

    /**
     * Casts de atributos para tipos corretos
     */
    protected $casts = [
        'valor_max_compra' => 'decimal:2',
        'data_nascimento'  => 'date:Y-m-d',
    ];

    // ===================================================================
    // RELACIONAMENTOS
    // ===================================================================

    /**
     * Corretor responsável pela oportunidade
     */
    public function corretor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'corretor_id');
    }

    /**
     * Atividades (ligações, visitas, etc.)
     */
    public function activities(): HasMany
    {
        return $this->hasMany(\App\Models\Activity::class);
    }

    /**
     * Contratos ou propostas
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(\App\Models\Contrato::class);
    }

    // ===================================================================
    // ACCESSORS & MUTATORS
    // ===================================================================

    /**
     * Formata o valor máximo como moeda brasileira
     */
    public function getValorMaxCompraFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_max_compra, 2, ',', '.');
    }

    /**
     * Salva e-mail em minúsculas e limpo
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    /**
     * Garante que o telefone tenha apenas números
     */
    public function setTelefoneAttribute($value): void
    {
        $this->attributes['telefone'] = preg_replace('/\D/', '', $value);
    }
}