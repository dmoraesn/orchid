{{-- resources/views/platform/kanban/kanban.blade.php --}}
@php
    /** @var \Illuminate\Support\Collection $kanbanData */
    $stageColors = [
        'Novo Lead / Sem Atendimento' => 'bg-blue-500',
        'Qualificação / Em Atendimento' => 'bg-cyan-500',
        'Apresentação / Visita' => 'bg-amber-500',
        'Proposta / Negociação' => 'bg-purple-500',
        'Formalização (Arras)' => 'bg-indigo-500',
        'Fechado Ganho' => 'bg-green-600',
        'Perdido (Lost)' => 'bg-red-600',
    ];
@endphp

<div class="p-4">

    {{-- Indicador de status --}}
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <x-orchid-icon path="check" class="me-2"/>
        <div><strong>Kanban ativo:</strong> arraste os leads para mudar de etapa!</div>
    </div>

    {{-- Kanban responsivo --}}
    <div id="kanban-board" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7 gap-3">
        @foreach($stages as $stage)
            <div class="kanban-column rounded-xl bg-gray-50 border border-gray-200 p-2 shadow-sm"
                 data-stage="{{ $stage }}">
                {{-- Cabeçalho da coluna --}}
                <div class="rounded-md text-white text-center py-2 mb-2 {{ $stageColors[$stage] ?? 'bg-gray-400' }}">
                    <div class="font-semibold text-sm leading-tight">{{ $stage }}</div>
                    <div class="text-xs opacity-90">
                        {{ $kanbanData[$stage]->count() }} {{ Str::plural('lead', $kanbanData[$stage]->count()) }}
                    </div>
                </div>

                {{-- Itens --}}
                <div class="kanban-items space-y-2 min-h-[80px]">
                    @forelse($kanbanData[$stage] as $lead)
                        <div class="kanban-item bg-white p-3 rounded-lg shadow-sm border hover:shadow-md transition cursor-grab"
                             data-id="{{ $lead->id }}">
                            <div class="font-semibold text-sm text-gray-800 truncate">
                                {{ $lead->nome_lead ?? 'Sem nome' }}
                            </div>
                            <div class="text-xs text-gray-500 truncate">
                                {{ $lead->email ?? 'Sem e-mail' }}
                            </div>
                            <div class="text-xs text-gray-700 font-medium mt-1">
                                R$ {{ number_format($lead->valor ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs text-gray-400 italic py-2">Sem leads</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Script SortableJS --}}
@push('scripts')
<script type="module">
    import Sortable from 'sortablejs/modular/sortable.complete.esm.js';

    document.querySelectorAll('.kanban-column').forEach(column => {
        const stage = column.dataset.stage;
        const container = column.querySelector('.kanban-items');

        new Sortable(container, {
            group: 'kanban',
            animation: 150,
            onAdd: async function (evt) {
                const leadId = evt.item.dataset.id;
                const newStage = stage;

                try {
                    const response = await fetch('{{ route('platform.opportunity.update_stage') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            opportunity_id: leadId,
                            new_stage: newStage
                        })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast('Etapa atualizada com sucesso!', 'success');
                    } else {
                        throw new Error('Falha ao atualizar');
                    }
                } catch (e) {
                    console.error(e);
                    showToast('Erro ao atualizar etapa.', 'error');
                }
            },
        });
    });

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-5 right-5 z-50 px-4 py-2 rounded text-white shadow ${
            type === 'success' ? 'bg-green-600' :
            type === 'error' ? 'bg-red-600' : 'bg-gray-700'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
</script>
@endpush
