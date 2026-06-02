{{--
    partials/form/admin-notes.blade.php
    Internal admin notes textarea and the Preview Question button.
--}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-newspaper-fill text-secondary me-2"></i>
            Admin Notes
            <span class="text-muted fw-normal small ms-1">(Internal)</span>
        </h6>
    </div>

    <div class="card-body p-3">
        <textarea
            wire:model="adminNotes"
            class="form-control border-0 bg-light"
            rows="4"
            placeholder="Internal notes — not visible to students..."></textarea>
    </div>

    <div class="card-footer bg-white p-3">
        <button type="button"
            class="btn btn-outline-info"
            wire:click="showPreview"
            wire:loading.attr="disabled">

            <span wire:loading wire:target="showPreview">
                <span class="spinner-border spinner-border-sm me-1"></span>Validating...
            </span>
            <span wire:loading.remove wire:target="showPreview">
                <i class="ri ri-eye-fill me-1"></i>Preview Question
            </span>
        </button>
    </div>
</div>
