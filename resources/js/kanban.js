document.addEventListener('DOMContentLoaded', () => {
    // ======= Botão de filtro recolhível =======
    const btn = document.getElementById('toggle-filters');
    const filters = document.getElementById('filters');

    if (btn && filters) {
        btn.addEventListener('click', () => {
            filters.classList.toggle('hidden');
            filters.classList.toggle('animate-fade-in');
        });
    }

    // ======= Drag & Drop =======
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-items');
    let draggedCard = null;

    cards.forEach(card => {
        card.addEventListener('dragstart', (e) => {
            draggedCard = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            draggedCard = null;
            updateAllCounters();
        });
    });

    columns.forEach(column => {
        column.addEventListener('dragover', (e) => {
            e.preventDefault();
            column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', () => {
            column.classList.remove('drag-over');
        });

        column.addEventListener('drop', async (e) => {
            e.preventDefault();
            column.classList.remove('drag-over');

            if (draggedCard) {
                column.appendChild(draggedCard);

                const leadId = draggedCard.dataset.leadId;
                const newStageId = column.dataset.stage;

                // animação rápida para feedback visual
                draggedCard.classList.add('animate-move');
                setTimeout(() => draggedCard.classList.remove('animate-move'), 300);

                try {
                    const response = await fetch(`/kanban/move-lead`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ lead_id: leadId, stage_id: newStageId })
                    });

                    if (!response.ok) throw new Error('Erro ao mover lead');

                    // Atualiza contadores visuais
                    updateAllCounters();
                } catch (err) {
                    console.error(err);
                    alert('Erro ao atualizar lead. Verifique sua conexão.');
                }
            }
        });
    });

    // ======= Atualiza contadores das colunas =======
    function updateAllCounters() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const count = column.querySelectorAll('.kanban-card').length;
            const counter = column.querySelector('.kanban-counter');
            if (counter) {
                counter.textContent = `${count} lead${count !== 1 ? 's' : ''}`;
            }
        });
    }

    // Atualiza inicialmente
    updateAllCounters();
});
