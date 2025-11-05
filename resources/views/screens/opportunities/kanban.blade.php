@extends('platform::app')

@section('title', 'Kanban de Oportunidades')

@pushOnce('styles')
    {{-- CSS customizado do Kanban --}}
    @vite(['resources/css/kanban.css'])
@endpushOnce

@pushOnce('scripts')
    {{-- JS customizado do Kanban --}}
    @vite(['resources/js/kanban.js'])
@endpushOnce

@section('content')
<div class="kanban-container p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Kanban de Oportunidades</h1>
            <p class="text-sm text-gray-500">Visão Kanban do pipeline de vendas</p>
        </div>

        <a href="{{ route('platform.opportunities.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition">
           + Nova Oportunidade
        </a>
    </div>

    {{-- 🔽 Botão de filtro recolhível --}}
    <button id="toggle-filters"
            class="mb-4 flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17V13.414L3.293 6.707A1 1 0 013 6V4z" />
        </svg>
        Filtros
    </button>

    <div id="filters" class="bg-white p-4 rounded-xl shadow-sm mb-6 hidden">
        {!! $filters ?? '' !!}
    </div>

    {{-- 🔽 Área do Kanban --}}
    <div id="kanbanBoard" class="kanban-board flex gap-4 overflow-x-auto pb-4">
        @foreach($stages as $stage)
            <div class="kanban-column bg-gray-50 rounded-xl shadow-sm min-w-[280px] flex-shrink-0"
                 data-stage="{{ $stage['id'] }}">
     <div class="bg-indigo-600 text-white text-sm font-medium px-3 py-2 rounded-t-xl flex justify-between items-center">
    <span>{{ $stage['name'] }}</span>
    <span class="kanban-counter opacity-75 text-xs">{{ count($stage['leads']) }} lead(s)</span>
</div>


                <div class="kanban-items p-3 space-y-2" data-stage="{{ $stage['id'] }}">
                    @forelse($stage['leads'] as $lead)
                        <div class="kanban-card bg-white p-3 rounded-lg shadow hover:shadow-md border border-gray-100 transition"
                             draggable="true"
                             data-lead-id="{{ $lead->id }}">
                            <p class="font-medium text-gray-800">{{ $lead->name }}</p>
                            <p class="text-xs text-gray-500">{{ $lead->email }}</p>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">Sem leads</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
