<div class="row kanban-board">
    @foreach($stages as $stage)
        @php
            $opportunities = $kanbanData[$stage];
            
            // Define a cor de fundo com base no estágio (simulando as cores do funil)
            $stageColor = match ($stage) {
                'Novo Lead / Sem Atendimento' => 'bg-gray-100',
                'Qualificação / Em Atendimento' => 'bg-blue-100',
                'Apresentação / Visita' => 'bg-yellow-100',
                'Proposta / Negociação' => 'bg-indigo-100',
                'Formalização (Arras)' => 'bg-purple-100',
                'Fechado Ganho' => 'bg-green-100',
                'Perdido (Lost)' => 'bg-red-100',
                default => 'bg-light',
            };
            
            // Simula o valor total do pipeline na coluna (visão gerencial)
            $totalValue = $opportunities->sum('valor_max_compra');
        @endphp

        <div class="col-md-3 mb-4 kanban-column" 
             data-stage="{{ $stage }}"
             style="min-width: 300px;">

            <div class="card {{ $stageColor }} shadow-sm">
                <div class="card-header border-0 pb-1">
                    <h5 class="mb-0 text-dark">{{ $stage }}</h5>
                    <small class="text-muted">
                        {{ $opportunities->count() }} Cards
                        @if($stage !== 'Fechado Ganho' && $stage !== 'Perdido (Lost)')
                            | R$ {{ number_format($totalValue, 2, ',', '.') }}
                        @endif
                    </small>
                </div>

                <div class="card-body kanban-body" id="stage-{{ Str::slug($stage) }}" 
                     style="height: 600px; overflow-y: auto;">

                    @foreach($opportunities as $opportunity)
                        @include('kanban.opportunity_card', ['opportunity' => $opportunity])
                    @endforeach

                    <!-- Espaço para o D&D -->
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    /* Estilos básicos para o Kanban */
    .kanban-board {
        flex-wrap: nowrap; /* Impede quebras de linha */
        overflow-x: auto; /* Permite scroll horizontal */
        padding-bottom: 15px;
    }
    .kanban-column {
        flex-shrink: 0;
        max-width: 350px;
    }
    .kanban-body {
        min-height: 150px;
        transition: background-color 0.3s;
    }
    .kanban-card {
        cursor: grab;
    }
    .kanban-card:active {
        cursor: grabbing;
    }
    /* Estilo para quando um card é arrastado para cima de uma coluna */
    .kanban-column.drag-over .card {
        border: 2px solid #3366ff;
    }
</style>

<!-- Script para a funcionalidade Drag and Drop (D&D) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const columns = document.querySelectorAll('.kanban-body');
        const cards = document.querySelectorAll('.kanban-card');

        let draggedCard = null;

        // 1. Configurar Cards para serem arrastáveis
        cards.forEach(card => {
            card.setAttribute('draggable', true);
            card.addEventListener('dragstart', function(e) {
                draggedCard = this;
                e.dataTransfer.setData('text/plain', this.dataset.id);
                setTimeout(() => this.style.opacity = '0.5', 0);
            });
            card.addEventListener('dragend', function() {
                this.style.opacity = '1';
                draggedCard = null;
            });
        });

        // 2. Configurar Colunas para receberem o drop
        columns.forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault(); // Necessário para permitir o drop
                this.closest('.kanban-column').classList.add('drag-over');
            });

            column.addEventListener('dragleave', function() {
                this.closest('.kanban-column').classList.remove('drag-over');
            });

            column.addEventListener('drop', function(e) {
                e.preventDefault();
                this.closest('.kanban-column').classList.remove('drag-over');
                
                if (draggedCard) {
                    // ID da Oportunidade
                    const opportunityId = draggedCard.dataset.id;
                    // Novo Estágio é o 'data-stage' da coluna pai
                    const newStage = this.closest('.kanban-column').dataset.stage; 

                    // Remove o card da coluna original e adiciona na nova
                    this.appendChild(draggedCard);

                    // 3. Chamada AJAX para o backend (Laravel/Orchid)
                    updateOpportunityStage(opportunityId, newStage);
                }
            });
        });

        // 4. Função AJAX para persistência de dados
        function updateOpportunityStage(id, stage) {
            // Obtém o token CSRF do meta tag (necessário para todas as requisições POST no Laravel)
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch("{{ route('platform.opportunity.update_stage') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify({
                    opportunity_id: id,
                    new_stage: stage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recarrega a tela para refletir o novo estado do banco de dados e atualizar o card
                    window.location.reload(); 
                } else {
                    alert('Erro ao atualizar a oportunidade.');
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('Erro na comunicação com o servidor.');
            });
        }
    });
</script>