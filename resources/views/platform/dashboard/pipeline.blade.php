<div class="card">
    <div class="card-header">
        <h5>Funil de Vendas</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            @foreach($stages as $stage)
                @php $count = $pipeline[$stage] ?? 0 @endphp
                <div class="col">
                    <div class="p-3 border rounded bg-light">
                        <small class="text-muted">{{ $stage }}</small>
                        <h4 class="mb-0">{{ $count }}</h4>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>