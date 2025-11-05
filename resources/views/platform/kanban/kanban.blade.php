<div class="kanban-board d-flex overflow-auto">
    @foreach($stages as $stage)
        <div class="kanban-column me-3" style="min-width: 300px;">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">{{ $stage }}</h6>
                    <small>{{ $kanbanData[$stage]->count() }} lead(s)</small>
                </div>
                <div class="card-body p-2 kanban-column-body"
                     data-stage="{{ $stage }}"
                     ondrop="drop(event)"
                     ondragover="allowDrop(event)">
                    @foreach($kanbanData[$stage] as $opportunity)
                        <div class="kanban-card mb-2 p-2 bg-light border rounded"
                             draggable="true"
                             ondragstart="drag(event)"
                             data-id="{{ $opportunity->id }}">
                            <strong>{{ $opportunity->nome_lead }}</strong><br>
                            <small>{{ $opportunity->email ?? 'Sem e-mail' }}</small><br>
                            <small class="text-muted">R$ {{ number_format($opportunity->valor_max_compra, 2, ',', '.') }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("opportunity_id", ev.target.dataset.id);
}

function drop(ev) {
    ev.preventDefault();
    const opportunityId = ev.dataTransfer.getData("opportunity_id");
    const newStage = ev.target.closest('.kanban-column-body').dataset.stage;

    fetch("{{ route('platform.opportunity.list') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({
            _method: 'PUT',
            opportunity_id: opportunityId,
            new_stage: newStage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>