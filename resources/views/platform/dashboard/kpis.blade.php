<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5>Total de Leads</h5>
                <h3>{{ $totalLeads }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5>Valor em Pipeline</h5>
                <h3>R$ {{ number_format($totalValue, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5>Fechados (Ganho)</h5>
                <h3>R$ {{ number_format($wonValue, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>