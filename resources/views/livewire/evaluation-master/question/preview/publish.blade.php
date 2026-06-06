{{--
    question/preview/publish.blade.php
    Status selection + Save button card inside the preview view.
    Uses Alpine two-way binding with Livewire's @entangle for pendingStatus.
--}}
<div class="card shadow-sm border-0 mb-4" >

    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-toggle-fill text-primary me-2"></i>Action
        </h6>
    </div>

    <div class="card-body p-4">



        {{-- Action buttons --}}
        <div class="d-grid gap-2 mt-4">
            <button type="button"
                class="btn btn-primary btn-lg"
                wire:click="save"
                wire:loading.attr="disabled">
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1"></span>Saving...
                </span>
                <span wire:loading.remove wire:target="save">
                    <i class="ri ri-save-fill me-2"></i>
                    Save
                </span>
            </button>

            <button type="button"
                class="btn btn-outline-secondary"
                wire:click="backToForm">
                <i class="ri ri-arrow-go-back-line me-1"></i>Back to Edit
            </button>
        </div>

    </div>
</div>
