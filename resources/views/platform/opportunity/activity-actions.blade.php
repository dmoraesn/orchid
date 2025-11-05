<div class="btn-group">
    <button type="button"
            class="btn btn-sm btn-info"
            data-bs-toggle="modal"
            data-bs-target="#activityModal"
            onclick="editActivity({{ $activity->id }})">
        <i class="bi bi-pencil"></i>
    </button>

    <form action="{{ route('platform.opportunity.activity.remove', [$activity->opportunity_id, $activity->id]) }}"
          method="POST"
          style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Excluir esta atividade?')">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>

<script>
function editActivity(id) {
    // Orchid recarrega o modal via async
    window.Orchid.modal('activityModal', {
        activity: { id: id }
    });
}
</script>