{{-- question-set/question-builder.blade.php — Step 2 + 3 builder --}}
<div>
    {{-- ══ Page Header ══════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-layout-grid-line text-success me-2"></i>
                Question Set Builder
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">


                        <a wire:click="backtoQuestionset" href="javascript:void(0)">Question Sets</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $setTitle }}</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            {{-- Quick status badge --}}
            <span
                class="badge fs-6 px-3 py-2
                @if ($setStatus === 'publish') bg-success
                @elseif($setStatus === 'draft') bg-warning text-dark
                @else bg-secondary @endif">
                {{ ucfirst($setStatus) }}
            </span>

            {{-- Status quick-toggle --}}
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    <i class="ri ri-toggle-line me-1"></i>Change Status
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><button class="dropdown-item" wire:click="updateSetStatus('draft')">
                            <i class="ri ri-edit-box-line text-warning me-2"></i>Draft</button></li>
                    <li><button class="dropdown-item" wire:click="updateSetStatus('publish')">
                            <i class="ri ri-cloud-line text-success me-2"></i>Publish</button></li>
                    <li><button class="dropdown-item" wire:click="updateSetStatus('unpublish')">
                            <i class="ri ri-cloud-off-line text-secondary me-2"></i>Unpublish</button></li>
                </ul>
            </div>
            <button type="button" wire:click="backtoQuestionset" class="btn btn-outline-secondary btn-sm">
                <i class="ri ri-arrow-left-line me-1"></i>Back to List
            </button>

        </div>
    </div>

    @include('livewire.evaluation-master.partials.alerts')

    {{-- Flash --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4">
            <i class="ri ri-checkbox-circle-line me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ── Left: Groups + Questions ─────────────────────── --}}
        <div class="col-lg-9">

            {{-- Step badges --}}
            <div class="d-flex gap-2 mb-4">
                <span class="badge bg-success px-3 py-2"><i class="ri ri-check-line me-1"></i>Step 1 Done</span>
                <span class="badge bg-primary px-3 py-2"><i class="ri ri-stack-line me-1"></i>Step 2 — Groups</span>
                <span class="badge bg-primary px-3 py-2"><i class="ri ri-question-line me-1"></i>Step 3 —
                    Questions</span>
            </div>

            {{-- ── Group Form (inline) ─────────────────────── --}}
            @if ($showGroupForm)
                @include('livewire.evaluation-master.question-set.partials.group-form')
            @endif

            {{-- ── Groups accordion ────────────────────────── --}}
            @if (count($groups) === 0 && !$showGroupForm)
                <div class="card border-dashed border-2 shadow-none text-center py-5 mb-4">
                    <div class="card-body">
                        <i class="ri ri-stack-line fs-1 text-muted mb-3 d-block"></i>
                        <h6 class="fw-semibold text-muted">No groups yet</h6>
                        <p class="small text-muted mb-3">Add your first group to start organising questions.</p>
                        <button type="button" wire:click="openNewGroup" class="btn btn-primary">
                            <i class="ri ri-add-large-line me-1"></i>Add First Group
                        </button>
                    </div>
                </div>
            @else
                @foreach ($groups as $groupIdx => $group)
                    @include('livewire.evaluation-master.question-set.partials.group-card', [
                        'group' => $group,
                        'groupIdx' => $groupIdx,
                    ])
                @endforeach

                @if (!$showGroupForm)
                    <button type="button" wire:click="openNewGroup" class="btn btn-outline-success w-100 mb-4">
                        <i class="ri ri-add-large-line me-1"></i>Add Another Group
                    </button>
                @endif
            @endif

        </div>

        {{-- ── Right: Summary sidebar ──────────────────────── --}}
        <div class="col-lg-3">
            @include('livewire.evaluation-master.question-set.partials.set-summary')
        </div>

    </div>

    {{-- ══ Question Picker Modal ════════════════════════════ --}}
    @if ($showPicker)
        @include('livewire.evaluation-master.question-set.partials.question-picker')
    @endif

    <div wire:loading.delay>@include('utilities.backdrop')</div>
</div>

@push('styles')
    <style>
        .border-dashed {
            border-style: dashed !important;
        }

        .group-card {
            transition: box-shadow .2s;
        }

        .group-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08) !important;
        }

        .question-row {
            transition: background .15s;
        }

        .question-row:hover {
            background: var(--bs-light);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush
