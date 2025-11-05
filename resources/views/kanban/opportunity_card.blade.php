<div class="card kanban-card mb-3 shadow-sm" 
     data-id="{{ $opportunity->id }}" 
     style="border-left: 5px solid {{ $opportunity->corretor->getHexColor() ?? '#5c7897' }};">
    
    <div class="card-body p-3">
        <!-- Título/Nome do Lead -->
        <h6 class="mb-1 text-primary">
            {{ Link::make($opportunity->nome_lead)
                ->route('platform.opportunity.edit', $opportunity) }}
        </h6>

        <!-- Valor e Etiqueta -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold text-dark">
                {{ $opportunity->getValorMaxCompraFormattedAttribute() }}
            </span>
            <span class="badge bg-light text-secondary">
                {{ $opportunity->fonte_lead ?? 'Manual' }}
            </span>
        </div>

        <!-- Corretor e Contato -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted">
                {{ $opportunity->corretor->name }}
            </small>

            <div class="btn-group btn-group-sm" role="group">
                @if($opportunity->telefone)
                <a href="tel:{{ $opportunity->telefone }}" class="btn btn-sm btn-outline-success p-1" title="Ligar">
                    <i class="bs bi-telephone"></i>
                </a>
                @endif
                @if($opportunity->email)
                <a href="mailto:{{ $opportunity->email }}" class="btn btn-sm btn-outline-info p-1" title="Email">
                    <i class="bs bi-envelope"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>