// resources/js/kanban.js
import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {
    initializeKanban();
    initializeLoadMore();
});

/**
 * Inicializa o Kanban com Drag & Drop
 */
function initializeKanban() {
    const columns = document.querySelectorAll('.kanban-column');
    if (!columns.length) return;

    columns.forEach(column => {
        const stage = column.dataset.stage;
        const container = column.querySelector('.kanban-items');
        if (!container) return;

        // Garante que novos cards carregados via "Ver mais" também sejam arrastáveis
        let sortableInstance = null;

        const initSortable = () => {
            if (sortableInstance) sortableInstance.destroy();

            sortableInstance = new Sortable(container, {
                group: 'kanban',
                animation: 180,
                ghostClass: 'kanban-ghost',
                dragClass: 'kanban-dragging',
                chosenClass: 'kanban-chosen',
                fallbackTolerance: 3,
                onStart: () => container.classList.add('drag-active'),
                onEnd: () => container.classList.remove('drag-active'),
                onAdd: async (evt) => {
                    const item = evt.item;
                    const leadId = item.dataset.id;
                    const newStage = stage;

                    if (!leadId) {
                        showToast('Lead sem ID. Não foi possível mover.', 'error');
                        evt.item.remove();
                        return;
                    }

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        if (!csrfToken) throw new Error('CSRF token não encontrado');

                        const response = await fetch('/admin/oportunidades/update-stage', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                opportunity_id: parseInt(leadId),
                                new_stage: newStage
                            })
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            showToast('Etapa atualizada!', 'success');
                            item.classList.add('animate-fadeIn');
                        } else {
                            throw new Error(result.message || 'Erro desconhecido');
                        }
                    } catch (error) {
                        console.error('Erro ao mover lead:', error);
                        showToast('Falha ao atualizar etapa.', 'error');
                        // Reverte o movimento
                        const oldList = evt.from;
                        oldList.insertBefore(item, evt.oldDraggableIndex ? oldList.children[evt.oldDraggableIndex] : null);
                    }
                }
            });
        };

        initSortable();

        // Observa adição de novos cards (via "Ver mais")
        new MutationObserver(() => {
            // Pequeno delay para garantir que o DOM foi atualizado
            setTimeout(initSortable, 50);
        }).observe(container, { childList: true });
    });
}

/**
 * Inicializa o botão "Ver mais"
 */
function initializeLoadMore() {
    document.querySelectorAll('.load-more-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const stage = btn.dataset.stage;
            const offset = parseInt(btn.dataset.offset) || 5;
            const container = document.querySelector(`#kanban-stage-${slugify(stage)}`);
            if (!container) return;

            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Carregando...';

            try {
                const response = await fetch(`/admin/oportunidades/load-more?etapa=${encodeURIComponent(stage)}&offset=${offset}`);
                if (!response.ok) throw new Error('Erro na requisição');

                const data = await response.json();

                if (data.leads && data.leads.length > 0) {
                    data.leads.forEach(lead => {
                        const card = createLeadCard(lead);
                        container.appendChild(card);
                    });

                    btn.dataset.offset = offset + data.leads.length;

                    if (!data.hasMore) {
                        btn.remove();
                    }
                } else {
                    btn.remove();
                }
            } catch (error) {
                console.error(error);
                showToast('Erro ao carregar mais leads.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        });
    });
}

/**
 * Cria card de lead (usado no "Ver mais")
 */
function createLeadCard(lead) {
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
        </div>
    `;
    return card;
}

/**
 * Slugify para IDs
 */
function slugify(text) {
    return text.toString().toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}

/**
 * Toast de feedback
 */
function showToast(message, type = 'info') {
    // Remove toast anterior
    document.querySelectorAll('.kanban-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `kanban-toast fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl text-white shadow-xl transition-all duration-500 opacity-0 translate-y-4`;

    const bg = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        info: 'bg-blue-600',
        warning: 'bg-yellow-600'
    }[type] || 'bg-gray-700';

    toast.classList.add(bg);
    toast.textContent = message;
    document.body.appendChild(toast);

    // Anima entrada
    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');
    });

    // Remove após 3s
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
