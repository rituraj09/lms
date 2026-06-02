{{--
    partials/preview/publish.blade.php
    Status selection + Save button card inside the preview view.
    Uses Alpine two-way binding with Livewire's @entangle for pendingStatus.
--}}
<div class="card shadow-sm border-0 mb-4"
    x-data="{
        selectedStatus: @entangle('pendingStatus'),
        init() {
            if (!this.selectedStatus) this.selectedStatus = 'draft';
        }
    }">

    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="ri ri-toggle-fill text-primary me-2"></i>Status &amp; Publishing
        </h6>
    </div>

    <div class="card-body p-4">

        {{-- Status icon preview --}}
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                style="width:80px;height:80px;"
                :class="{
                    'bg-warning bg-opacity-10':   selectedStatus === 'draft',
                    'bg-success bg-opacity-10':   selectedStatus === 'publish',
                    'bg-secondary bg-opacity-10': selectedStatus === 'unpublish'
                }">
                <i class="fs-1"
                    :class="{
                        'ri-edit-box-line text-warning':    selectedStatus === 'draft',
                        'ri-cloud-line text-success':       selectedStatus === 'publish',
                        'ri-cloud-off-line text-secondary': selectedStatus === 'unpublish'
                    }"></i>
            </div>

            <div class="fw-semibold mb-1">Will be saved as</div>

            <span class="badge px-3 py-2 fs-6"
                :class="{
                    'bg-warning text-dark': selectedStatus === 'draft',
                    'bg-success':           selectedStatus === 'publish',
                    'bg-secondary':         selectedStatus === 'unpublish'
                }"
                x-text="{
                    draft:     'Draft',
                    publish:   'Published',
                    unpublish: 'Unpublished'
                }[selectedStatus] ?? 'Draft'">
            </span>
        </div>

        <hr>

        {{-- Status radio options --}}
        <label class="form-label fw-medium small mb-2">Change Status</label>

        <div class="d-flex flex-column gap-2">

            @foreach ([
                ['value' => 'draft',     'label' => 'Draft',       'desc' => 'Not visible to students.',        'icon' => 'ri-edit-box-line',    'color' => 'warning'],
                ['value' => 'publish',   'label' => 'Publish',     'desc' => 'Visible to students.',            'icon' => 'ri-cloud-line',       'color' => 'success'],
                ['value' => 'unpublish', 'label' => 'Unpublish',   'desc' => 'Hidden from students.',           'icon' => 'ri-cloud-off-line',   'color' => 'secondary'],
            ] as $opt)
                <label class="status-option d-flex align-items-center gap-3 p-3 rounded border"
                    :class="selectedStatus === '{{ $opt['value'] }}'
                        ? 'border-{{ $opt['color'] }} bg-{{ $opt['color'] }} bg-opacity-10'
                        : 'border-secondary'">
                    <input type="radio"
                        x-model="selectedStatus"
                        value="{{ $opt['value'] }}"
                        class="form-check-input" />
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $opt['label'] }}</div>
                        <div class="small text-muted">{{ $opt['desc'] }}</div>
                    </div>
                    <i class="{{ $opt['icon'] }} text-{{ $opt['color'] }} fs-4"></i>
                </label>
            @endforeach

        </div>

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
                    Save as
                    <span x-text="{
                        draft:     'Draft',
                        publish:   'Published',
                        unpublish: 'Unpublished'
                    }[selectedStatus] ?? 'Draft'"></span>
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
