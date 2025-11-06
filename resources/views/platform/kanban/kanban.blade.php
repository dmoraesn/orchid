{{-- resources/views/platform/kanban/kanban.blade.php --}}

@extends('platform::layouts.app')

@section('title', 'Kanban de Oportunidades')

@vite([
    'resources/css/kanban.css',
    'resources/js/kanban.js',
])

@section('content')
<div class="p-4">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Kanban de Oportunidades</h2>

        <button id="toggleFilters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <x-heroicon-o-adjustments-vertical class="w-5 h-5" />
            Filtros
        </button>
    </div>

    {{-- Filtros (opcional) --}}
    <div id="filtersPanel" class="mb-4 hidden">
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
            <p class="text-sm italic text-gray-600">Filtros personalizados em breve…</p>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="kanban-container flex gap-4 overflow-x-auto pb-6">

        @foreach ($stages as $stage)
            @php
                $leads   = $kanbanData[$stage] ?? collect();
                $total   = Lead::where('etapa', $stage)->count();
                $visible = $leads;
            @endphp

            <div class="kanban-column bg-white rounded-xl border border-gray-200 shadow-sm p-3 flex-shrink-0"
                 style="min-width: 280px;"
                 data-stage="{{ $stage }}">

                {{-- Cabeçalho --}}
                <div class="kanban-header text-center mb-3">
                    <div class="font-semibold text-gray-800">{{ $stage }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $total }} {{ \Illuminate\Support\Str::plural('lead', $total) }}
                    </div>
                </div>

                {{-- Cards --}}
                <div class="kanban-items space-y-2 min-h-[80px]"
                     id="kanban-stage-{{ \Illuminate\Support\Str::slug($stage) }}">

                    @forelse ($visible as $lead)
                        <div class="kanban-card bg-gray-50 hover:bg-white border border-gray-200 rounded-lg p-3 shadow-sm transition hover:shadow-md cursor-grab"
                             data-id="{{ $lead->id }}">

                            <div class="text-center">
                                <x-heroicon-o-user class="w-6 h-6 text-blue-500 mx-auto mb-1" />
                                <div class="font-semibold text-gray-800 truncate">
                                    {{ $lead->nome ?? 'Sem nome' }}
                                </div>

                                <x-heroicon-o-envelope class="w-5 h-5 text-gray-400 mx-auto mt-1" />
                                <div class="text-xs text-gray-500 truncate">
                                    {{ $lead->email ?? 'Sem e-mail' }}
                                </div>

                                <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-600 mx-auto mt-1" />
                                <div class="text-xs font-medium text-green-700">
                                    R$ {{ number_format($lead->valor ?? 0, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs italic text-gray-400 py-3">
                            Nenhum lead nesta etapa
                        </div>
                    @endforelse
                </div>

                {{-- Ver mais --}}
                @if ($total > 5)
                    <button class="load-more-btn mt-3 w-full px-3 py-1.5 text-sm font-medium text-indigo-600 bg-white border border-indigo-300 rounded-md hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            data-stage="{{ $stage }}"
                            data-offset="5">
                        Ver mais
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('toggleFilters');
        const panel = document.getElementById('filtersPanel');
        btn?.addEventListener('click', () => panel.classList.toggle('hidden'));

        const slugify = str => str.toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');

        document.querySelectorAll('.load-more-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const stage = btn.dataset.stage;
                const offset = parseInt(btn.dataset.offset) || 5;
                const container = document.getElementById(`kanban-stage-${slugify(stage)}`);

                btn.disabled = true;
                const original = btn.textContent;
                btn.textContent = 'Carregando…';

                try {
                    const resp = await fetch(`/oportunidades/load-more?etapa=${encodeURIComponent(stage)}&offset=${offset}`);
                    const data = await resp.json();

                    if (data.leads?.length) {
                        data.leads.forEach(lead => {
                            const card = document.createElement('div');
                            card.className = 'kanban-card bg-gray-50 hover:bg-white border border-gray-200 rounded-lg p-3 shadow-sm transition hover:shadow-md cursor-grab animate-fadeIn';
                            card.dataset.id = lead.id;
                            card.innerHTML = `
                                <div class="text-center">
                                    <x-heroicon-o-user class="w-6 h-6 text-blue-500 mx-auto mb-1" />
                                    <div class="font-semibold text-gray-800 truncate">${lead.nome ?? 'Sem nome'}</div>
                                    <x-heroicon-o-envelope class="w-5 h-5 text-gray-400 mx-auto mt-1" />
                                    <div class="text-xs text-gray-500 truncate">${lead.email ?? 'Sem e-mail'}</div>
                                    <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-600 mx-auto mt-1" />
                                    <div class="text-xs font-medium text-green-700">R$ ${Number(lead.valor ?? 0).toFixed(2).replace('.', ',')}</div>
                                </div>`;
                            container.appendChild(card);
                        });
                        btn.dataset.offset = offset + data.leads.length;
                        if (!data.hasMore) btn.remove();
                    } else {
                        btn.remove();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Erro ao carregar mais leads.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = original;
                }
            });
        });
    });
</script>
@endpush
