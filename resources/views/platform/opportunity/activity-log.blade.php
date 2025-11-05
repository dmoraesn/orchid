<div class="timeline">
    @forelse($activities as $activity)
        <div class="timeline-item">
            <div class="timeline-badge {{ $activity->properties['attributes']['etapa_pipeline'] ?? '' ? 'bg-success' : 'bg-info' }}"></div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="timeline-title">
                        {{ $activity->description }}
                    </h6>
                    <small class="text-muted">
                        <i class="fa fa-clock-o"></i>
                        {{ $activity->created_at->diffForHumans() }}
                        por {{ $activity->causer?->name ?? 'Sistema' }}
                    </small>
                </div>
                @if($activity->properties['attributes']['etapa_pipeline'] ?? false)
                    <div class="timeline-body">
                        <p>
                            <strong>Etapa:</strong>
                            {{ $activity->properties['old']['etapa_pipeline'] ?? 'Nova' }}
                            → {{ $activity->properties['attributes']['etapa_pipeline'] }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhuma atividade registrada.</p>
    @endforelse
</div>