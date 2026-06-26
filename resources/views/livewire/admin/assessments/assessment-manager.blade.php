@php
    use App\Helper\Globals;
    $languages = Globals::LANGUAGES;
@endphp

<div class="assessment-manager-wrapper">

    {{-- ══════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════════════════════════ --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show
                    d-flex align-items-center gap-2 mb-4"
             role="alert">
            <i class="ri ri-checkbox-circle-line fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show
                    d-flex align-items-center gap-2 mb-4"
             role="alert">
            <i class="ri ri-error-warning-line fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="ri ri-error-warning-fill me-2 fs-5"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════
       ██████  VIEW 0 — ASSESSMENT LIST
  ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'list')

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-draft-line text-primary me-2"></i>
                    Assessments
                </h4>
                <p class="text-muted small mb-0">
                    Manage all assessments and their Sections.
                </p>
            </div>
            @can('assessment.create')
                <button type="button" wire:click="createAssessment" class="btn btn-primary btn-sm shadow-sm">
                    <i class="ri ri-add-large-line me-1"></i>
                    New Assessment
                </button>
            @endcan
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="ri ri-search-line text-muted"></i>
                        </span>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   class="form-control"
                                   placeholder="Search by title or code...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="typeFilter" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($assessmentTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        {{-- ── Status Filter — Radio Toggle Bar ──────────── --}}
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" wire:model.live="statusFilter"
                                   value="" id="sf-all" autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm" for="sf-all">All</label>

                            <input type="radio" class="btn-check" wire:model.live="statusFilter"
                                   value="draft" id="sf-draft" autocomplete="off">
                            <label class="btn btn-outline-warning btn-sm" for="sf-draft">Draft</label>

                            <input type="radio" class="btn-check" wire:model.live="statusFilter"
                                   value="publish" id="sf-publish" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="sf-publish">Live</label>

                            <input type="radio" class="btn-check" wire:model.live="statusFilter"
                                   value="unpublish" id="sf-unpublish" autocomplete="off">
                            <label class="btn btn-outline-danger btn-sm" for="sf-unpublish">Off</label>
                        </div>
                        {{-- ────────────────────────────────────────────────── --}}
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10 / page</option>
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div wire:loading.flex class="align-items-center gap-2 text-primary mb-3">
            <div class="spinner-border spinner-border-sm"></div>
            <small>Loading…</small>
        </div>

        {{-- List --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                @forelse ($assessments as $assessment)
                    <div class="border-bottom" wire:key="ass-{{ $assessment->id }}">

                        {{-- ── Main Row ──────────────────────────────────── --}}
                        <div class="px-4 py-3">
                            <div class="d-flex align-items-start justify-content-between gap-3">

                                {{-- Left: Title + Meta --}}
                                <div class="flex-1">

                                    {{-- Title + Status Badges --}}
                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="fw-semibold text-dark fs-6">
                                        {{ $assessment->title }}
                                    </span>


                                        {{-- Type Badge --}}
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ strtoupper($assessment->assessment_type_id) }}
                                    </span>
                                    </div>

                                    {{-- Meta Row --}}
                                    <div class="d-flex flex-wrap gap-3 mb-3">
                                        <small class="text-muted">
                                            <i class="ri ri-barcode-line me-1"></i>
                                            {{ $assessment->assessment_code }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="ri ri-group-line me-1"></i>
                                            {{ $assessment->assessment_groups_count }} Group(s)
                                        </small>
                                        <small class="text-muted">
                                            <i class="ri ri-trophy-line me-1"></i>
                                            {{ $assessment->total_marks }} Marks
                                        </small>
                                        <small class="text-muted">
                                            <i class="ri ri-crosshair-line me-1"></i>
                                            Pass: {{ $assessment->passing_marks }}
                                        </small>
                                        @if ($assessment->duration_minutes)
                                            <small class="text-muted">
                                                <i class="ri ri-timer-line me-1"></i>
                                                {{ $assessment->duration_minutes }} min
                                            </small>
                                        @endif
                                        <small class="text-muted">
                                            <i class="ri ri-user-line me-1"></i>
                                            {{ $assessment->ageGroup?->name ?? '—' }}
                                        </small>
                                    </div>

                                    {{-- ── Assessment Settings Row ───────────────── --}}
                                    <div class="d-flex flex-wrap gap-2">

                                        {{-- Max Attempts --}}
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle"
                                              data-bs-toggle="tooltip"
                                              title="Max Attempts">
                                        <i class="ri ri-repeat-line me-1"></i>
                                        {{ $assessment->max_attempts ?? 1 }}x Attempt
                                    </span>

                                        {{-- Negative Marking --}}
                                        @if ($assessment->has_negative_mark)
                                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle"
                                                  data-bs-toggle="tooltip"
                                                  title="Negative Marking Enabled">
                                            <i class="ri ri-subtract-line me-1"></i>
                                            Negative Mark
                                        </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle"
                                                  data-bs-toggle="tooltip"
                                                  title="No Negative Marking">
                                            <i class="ri ri-subtract-line me-1"></i>
                                            No Negative
                                        </span>
                                        @endif

                                        {{-- Shuffle Sections --}}
                                        <span class="badge rounded-pill
                                        {{ $assessment->shuffle_sections
                                            ? 'bg-info-subtle text-info border border-info-subtle'
                                            : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                              data-bs-toggle="tooltip"
                                              title="Shuffle Sections">
                                        <i class="ri ri-shuffle-line me-1"></i>
                                        {{ $assessment->shuffle_sections ? 'Shuffle On' : 'Shuffle Off' }}
                                    </span>

                                        {{-- Show Result --}}
                                        <span class="badge rounded-pill
                                        {{ $assessment->show_result_immediately
                                            ? 'bg-success-subtle text-success border border-success-subtle'
                                            : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                              data-bs-toggle="tooltip"
                                              title="Show Result Immediately">
                                        <i class="ri ri-bar-chart-line me-1"></i>
                                        {{ $assessment->show_result_immediately ? 'Instant Result' : 'Result Later' }}
                                    </span>

                                        {{-- Show Correct Answers --}}
                                        @if ($assessment->show_result_immediately)
                                            <span class="badge rounded-pill
                                            {{ $assessment->show_correct_answers
                                                ? 'bg-success-subtle text-success border border-success-subtle'
                                                : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                                  data-bs-toggle="tooltip"
                                                  title="Show Correct Answers">
                                            <i class="ri ri-checkbox-circle-line me-1"></i>
                                            {{ $assessment->show_correct_answers ? 'Answers Shown' : 'Answers Hidden' }}
                                        </span>

                                            {{-- Show Explanations --}}
                                            @if ($assessment->show_correct_answers)
                                                <span class="badge rounded-pill
                                                {{ $assessment->show_explainations
                                                    ? 'bg-info-subtle text-info border border-info-subtle'
                                                    : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                                      data-bs-toggle="tooltip"
                                                      title="Show Explanations">
                                                <i class="ri ri-book-open-line me-1"></i>
                                                {{ $assessment->show_explainations ? 'Explanations On' : 'Explanations Off' }}
                                            </span>
                                            @endif
                                        @endif

                                    </div>
                                    {{-- ── End Settings Row ──────────────────────── --}}

                                </div>

                                {{-- Right: Action Buttons --}}
                                <div class="d-flex gap-2 flex-shrink-0">

                                    {{-- ── NEW: Lock Badge when has attempts ─────── --}}
                                    @if ($assessment->attempts_count > 0)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle d-flex align-items-center gap-1"
                                              data-bs-toggle="tooltip"
                                              title="{{ $assessment->attempts_count }} attempt(s) made. Edit & Builder are locked.">
            <i class="ri ri-lock-line"></i>
            {{ $assessment->attempts_count }} Attempts
        </span>
                                    @endif
                                    {{-- ────────────────────────────────────────────── --}}
                                    <a href="{{ route('admin.assessments.preview', encrypt($assessment->id)) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="ri ri-eye-line"></i>
                                    </a>
                                    {{-- Builder Button — Disabled if locked --}}
                                    <button type="button"
                                            wire:click="openBuilder({{ $assessment->id }})"
                                            class="btn btn-sm {{ $assessment->attempts_count > 0 ? 'btn-outline-secondary disabled' : 'btn-outline-info' }}"
                                            data-bs-toggle="tooltip"
                                            title="{{ $assessment->attempts_count > 0 ? 'Locked: Students have attempted this assessment' : 'Open Builder' }}"
                                        {{ $assessment->attempts_count > 0 ? 'disabled' : '' }}>
                                        <i class="ri {{ $assessment->attempts_count > 0 ? 'ri-lock-line' : 'ri-tools-line' }} me-1"></i>
                                        Builder
                                    </button>

                                    {{-- Edit Button — Disabled if locked --}}
                                    <button type="button"
                                            wire:click="editAssessment({{ $assessment->id }})"
                                            class="btn btn-sm {{ $assessment->attempts_count > 0 ? 'btn-outline-secondary disabled' : 'btn-outline-primary' }}"
                                            data-bs-toggle="tooltip"
                                            title="{{ $assessment->attempts_count > 0 ? 'Locked: Cannot edit after students have attempted' : 'Edit Assessment' }}"
                                        {{ $assessment->attempts_count > 0 ? 'disabled' : '' }}>
                                        <i class="ri {{ $assessment->attempts_count > 0 ? 'ri-lock-line' : 'ri-pencil-line' }}"></i>
                                    </button>

                                    {{-- Status Radio Toggle — ALWAYS AVAILABLE (only publish/unpublish when locked) --}}
                                    <div class="btn-group btn-group-sm" role="group">
                                        @php
                                            $statusOptions = [
                                                'draft'     => ['warning',   'ri-draft-line',           'Draft'],
                                                'publish'   => ['success',   'ri-checkbox-circle-line', 'Live'],
                                                'unpublish' => ['secondary', 'ri-eye-off-line',         'off'],
                                            ];
                                            $isLocked = $assessment->attempts_count > 0;
                                        @endphp

                                        @foreach ($statusOptions as $val => [$color, $icon, $label])
                                            @php
                                                // When locked, Draft is disabled
                                                $isDisabled = $isLocked && $val === 'draft';
                                            @endphp
                                            <input
                                                type="radio"
                                                class="btn-check"
                                                name="status-{{ $assessment->id }}"
                                                id="st-{{ $assessment->id }}-{{ $val }}"
                                                value="{{ $val }}"
                                                autocomplete="off"
                                                @if(!$isDisabled) wire:click="changeStatus({{ $assessment->id }}, '{{ $val }}')" @endif
                                                {{ $assessment->status === $val ? 'checked' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}
                                            >
                                            <label
                                                class="btn btn-outline-{{ $color }} {{ $isDisabled ? 'opacity-50' : '' }}"
                                                for="st-{{ $assessment->id }}-{{ $val }}"
                                                data-bs-toggle="tooltip"
                                                title="{{ $isDisabled ? 'Cannot set to Draft after attempts made' : $label }}"
                                                style="font-size: .7rem; padding: .2rem .5rem;"
                                            >
                                                <i class="ri {{ $icon }}"></i>
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>

                                    {{-- Delete — Only if no attempts AND not published --}}
                                    @if ($assessment->attempts_count === 0 && $assessment->status !== 'publish')
                                        <button type="button"
                                                wire:click="deleteAssessment({{ $assessment->id }})"
                                                wire:confirm="Delete this assessment permanently?"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                title="Delete Assessment">
                                            <i class="ri ri-delete-bin-line"></i>
                                        </button>
                                    @endif

                                </div>

                            </div>
                        </div>
                        {{-- ── End Main Row ──────────────────────────────── --}}

                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ri ri-draft-line" style="font-size:3rem;opacity:.3;"></i>
                        <h6 class="mt-3 fw-semibold">No Assessments Found</h6>
                        <p class="small mb-4">Click "New Assessment" to get started.</p>
                        @can('assessment.create')
                            <button type="button" wire:click="createAssessment" class="btn btn-primary btn-sm">
                                <i class="ri ri-add-large-line me-1"></i>
                                New Assessment
                            </button>
                        @endcan
                    </div>
                @endforelse

            </div>
        </div>

        <div class="mt-4">
            {{ $assessments->links('pagination::bootstrap-5') }}
        </div>

    @endif
    {{-- /list --}}


    {{-- ══════════════════════════════════════════════════════════════
         ██████  VIEW 1 — ASSESSMENT FORM
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'form')

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-draft-line text-primary me-2"></i>
                    {{ $assessmentId ? 'Edit Assessment' : 'New Assessment' }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="#" wire:click.prevent="backToList">Assessments</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $assessmentId ? 'Edit' : 'New' }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button type="button" wire:click="cancelForm" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Cancel
                </button>
                <button type="button" wire:click="saveAssessment" wire:loading.attr="disabled"
                        class="btn btn-primary btn-sm shadow-sm">
                    <span wire:loading wire:target="saveAssessment">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving…
                    </span>
                    <span wire:loading.remove wire:target="saveAssessment">
                        <i class="ri ri-arrow-right-line me-1"></i>
                        Save & Go to Builder
                    </span>
                </button>
            </div>
        </div>

        <form wire:submit.prevent="saveAssessment">
            <div class="row g-4">

                <div class="col-lg-8">

                    {{-- Basic Info --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-information-line text-primary me-2"></i>
                                Assessment Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Assessment Code
                                    </label>
                                    <div
                                        class="form-control bg-light text-muted small
                                                d-flex align-items-center gap-2">
                                        <i class="ri ri-barcode-line"></i>
                                        {{ $assessment_code }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Assessment Type
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="assessment_type_id"
                                            class="form-select @error('assessment_type_id') is-invalid @enderror">
                                        <option value="">— Select Type —</option>
                                        @foreach ($assessmentTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('assessment_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Age Group
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="age_group_id"
                                            class="form-select @error('age_group_id') is-invalid @enderror">
                                        <option value="">— Select Age Group —</option>
                                        @foreach ($ageGroups as $ag)
                                            <option value="{{ $ag['id'] }}">{{ $ag['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('age_group_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- ── Cover Image ─────────────────────────────────────── --}}
                                <div class="col-12">
                                    <label class="form-label fw-medium small">
                                        Cover Image
                                        <span class="text-muted fw-normal">(Optional)</span>
                                    </label>

                                    <div class="d-flex align-items-start gap-3 flex-wrap">

                                        {{-- Preview Box --}}
                                        <div class="border rounded d-flex align-items-center justify-content-center bg-light overflow-hidden"
                                             style="width: 160px; height: 110px; flex-shrink: 0;">

                                            @if ($cover_image_file)
                                                {{-- Newly selected (not yet saved) --}}
                                                <img src="{{ $cover_image_file->temporaryUrl() }}"
                                                     alt="Preview"
                                                     class="img-fluid w-100 h-100 object-fit-cover">

                                            @elseif ($cover_image_path && !$removeCoverImage)
                                                {{-- Existing image from DB --}}

                                                <img src="{{ Storage::url($cover_image_path) }}" alt="ICovermage"
                                                     class="w-100 h-100 object-fit-cover" />
                                            @else
                                                {{-- Placeholder --}}
                                                <div class="text-center text-muted small px-2">
                                                    <i class="ri-image-add-line fs-2 d-block mb-1"></i>
                                                    No image
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Controls --}}
                                        <div class="d-flex flex-column gap-2 justify-content-center">

                                            {{-- Upload input --}}
                                            <div>
                                                <input type="file"
                                                       wire:model="cover_image_file"
                                                       id="coverImageInput"
                                                       accept="image/jpg,image/jpeg,image/png,image/webp"
                                                       class="d-none">

                                                <label for="coverImageInput"
                                                       class="btn btn-sm btn-outline-primary mb-0"
                                                       style="cursor: pointer;">
                                                    <i class="ri-upload-2-line me-1"></i>
                                                    {{ ($cover_image_path && !$removeCoverImage) || $cover_image_file
                                                        ? 'Change Image'
                                                        : 'Upload Image' }}
                                                </label>
                                            </div>

                                            {{-- Remove button — only show when there is an image --}}
                                            @if (($cover_image_path && !$removeCoverImage) || $cover_image_file)
                                                <button type="button"
                                                        wire:click="removeCoverImage"
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-6-line me-1"></i>
                                                    Remove
                                                </button>
                                            @endif

                                            {{-- Upload progress spinner --}}
                                            <div wire:loading wire:target="cover_image_file"
                                                 class="text-primary small">
                                                <span class="spinner-border spinner-border-sm me-1"
                                                      role="status" aria-hidden="true"></span>
                                                Uploading...
                                            </div>

                                            {{-- Hint text --}}
                                            <small class="text-muted">
                                                JPG, PNG, WEBP &bull; Max 2 MB
                                            </small>

                                            {{-- Validation error --}}
                                            @error('cover_image_file')
                                            <div class="text-danger small">
                                                <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                {{-- ── End Cover Image ──────────────────────────────────── --}}
                                <div class="col-12">
                                    <label class="form-label fw-medium small">
                                        Title
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model="title"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Assessment title...">
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-medium small">
                                        Instructions
                                        <span class="text-muted fw-normal">(Optional)</span>
                                    </label>
                                    <textarea wire:model="instructions" class="form-control" rows="3"
                                              placeholder="Instructions shown to students...">
                                    </textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Marks & Duration --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-trophy-line text-primary me-2"></i>
                                Marks & Duration
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label fw-medium small">
                                        Total Marks
                                    </label>
                                    <div class="form-control bg-light text-success fw-semibold">
                                        {{ $total_marks }}
                                    </div>
                                    <small class="text-muted">
                                        Auto-calculated
                                    </small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium small">
                                        Passing Marks
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" wire:model="passing_marks"
                                           class="form-control @error('passing_marks') is-invalid @enderror"
                                           min="0" step="0.5">
                                    @error('passing_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium small">
                                        Duration (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" wire:model="duration_minutes" class="form-control"
                                               min="1" placeholder="e.g. 60">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium small d-block"> Has Negative Mark?
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox"
                                               wire:model.live="has_negative_mark" id="negMark" role="switch">
                                        <label class="form-check-label small" for="negMark">
                                            {{ $has_negative_mark ? 'Enabled' : 'Disabled' }}
                                        </label>
                                    </div>
                                </div>



                            </div>

                        </div>


                    </div>
                </div>
                <div class="col-lg-4">
                    {{-- Assessment Settings --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-settings-4-line text-primary me-2"></i>
                                Assessment Settings
                            </h6>
                        </div>
                        <div class="card-body p-3">

                            {{-- Max Attempts --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium small">
                                    <i class="ri ri-repeat-line me-1 text-primary"></i>
                                    Max Attempts
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       wire:model="max_attempts"
                                       class="form-control form-control-sm @error('max_attempts') is-invalid @enderror"
                                       min="1"
                                       placeholder="e.g. 1">
                                @error('max_attempts')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Number of times a student can attempt this assessment.
                                </small>
                            </div>

                            <hr class="my-3">

                            {{-- Shuffle Sections --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 small fw-medium">
                                            <i class="ri ri-shuffle-line me-1 text-primary"></i>
                                            Shuffle Sections
                                        </p>
                                        <small class="text-muted">
                                            Randomize the order of sections for each attempt.
                                        </small>
                                    </div>
                                    <div class="form-check form-switch ms-3 mb-0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               wire:model="shuffle_sections"
                                               id="shuffleSections">
                                        <label class="form-check-label small" for="shuffleSections">
                                            {{ $shuffle_sections ? 'Yes' : 'No' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            {{-- Show Result Immediately --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 small fw-medium">
                                            <i class="ri ri-bar-chart-line me-1 text-success"></i>
                                            Show Result Immediately
                                        </p>
                                        <small class="text-muted">
                                            Display result to student right after submission.
                                        </small>
                                    </div>
                                    <div class="form-check form-switch ms-3 mb-0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               wire:model.live="show_result_immediately"
                                               id="showResult">
                                        <label class="form-check-label small" for="showResult">
                                            {{ $show_result_immediately ? 'Yes' : 'No' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Show Correct Answers — only if show_result_immediately --}}
                            @if ($show_result_immediately)

                                <hr class="my-3">

                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="mb-0 small fw-medium">
                                                <i class="ri ri-checkbox-circle-line me-1 text-success"></i>
                                                Show Correct Answers
                                            </p>
                                            <small class="text-muted">
                                                Show the correct answer along with the result.
                                            </small>
                                        </div>
                                        <div class="form-check form-switch ms-3 mb-0">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   role="switch"
                                                   wire:model.live="show_correct_answers"
                                                   id="showCorrectAnswers">
                                            <label class="form-check-label small" for="showCorrectAnswers">
                                                {{ $show_correct_answers ? 'Yes' : 'No' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Show Explanations — only if show_correct_answers --}}
                                @if ($show_correct_answers)

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="mb-0 small fw-medium">
                                                    <i class="ri ri-book-open-line me-1 text-info"></i>
                                                    Show Explanations
                                                </p>
                                                <small class="text-muted">
                                                    Show answer explanations with correct answers.
                                                </small>
                                            </div>
                                            <div class="form-check form-switch ms-3 mb-0">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       role="switch"
                                                       wire:model="show_explainations"
                                                       id="showExplainations">
                                                <label class="form-check-label small" for="showExplainations">
                                                    {{ $show_explainations ? 'Yes' : 'No' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                @endif

                            @endif

                        </div>
                    </div>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-settings-3-line text-primary me-2"></i>
                                Status & Save
                            </h6>
                        </div>
                        <div class="card-body p-3">

                            <label class="form-label fw-medium small">Status</label>
                            <div class="d-flex gap-3 mb-3">
                                @foreach ([
        'draft' => ['secondary', 'ri-draft-line'],
        'publish' => ['success', 'ri-checkbox-circle-line'],
        'unpublish' => ['warning', 'ri-eye-off-line'],
    ] as $val => [$color, $icon])
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="status"
                                               value="{{ $val }}" id="status-{{ $val }}">
                                        <label class="form-check-label small" for="status-{{ $val }}">
                                            <i class="ri {{ $icon }} text-{{ $color }} me-1"></i>
                                            {{ ucfirst($val) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label fw-medium small">Admin Note</label>
                            <textarea wire:model="admin_note" class="form-control border-0 bg-light" rows="3"
                                      placeholder="Internal note...">
                            </textarea>
                        </div>
                        <div class="card-footer bg-white p-3">
                            <button type="button" wire:click="saveAssessment" wire:loading.attr="disabled"
                                    class="btn btn-primary w-100">
                                <span wire:loading wire:target="saveAssessment">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Saving…
                                </span>
                                <span wire:loading.remove wire:target="saveAssessment">
                                    <i class="ri ri-arrow-right-line me-1"></i>
                                    Save & Go to Builder
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    @endif
    {{-- /form --}}


    {{-- ══════════════════════════════════════════════════════════════
         ██████  VIEW 2 — ASSESSMENT BUILDER
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'builder')

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-tools-line text-primary me-2"></i>
                    Assessment Builder
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="#" wire:click.prevent="backToList">Assessments</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" wire:click.prevent="backToForm">{{ $title }}</a>
                        </li>
                        <li class="breadcrumb-item active">Builder</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <button type="button" wire:click="backToForm" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-pencil-line me-1"></i> Edit Info
                </button>
                <button type="button" wire:click="openGroupPicker" class="btn btn-outline-primary btn-sm">
                    <i class="ri ri-add-large-line me-1"></i>
                    Add Sections
                </button>
                <button type="button" wire:click="saveBuilder" wire:loading.attr="disabled"
                        class="btn btn-primary btn-sm shadow-sm">
                    <span wire:loading wire:target="saveBuilder">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving…
                    </span>
                    <span wire:loading.remove wire:target="saveBuilder">
                        <i class="ri ri-save-line me-1"></i>
                        Save Builder
                    </span>
                </button>
            </div>
        </div>

        <div class="row g-4">

            {{-- Left — Groups + Questions --}}
            <div class="col-lg-8">

                @if (empty($assessmentGroups))
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-5 text-muted">
                            <i class="ri ri-layout-grid-line" style="font-size:3rem;opacity:.3;"></i>
                            <h6 class="mt-3 fw-semibold">No Sections Added</h6>
                            <p class="small mb-4">
                                Click "Add Section" to start building.
                            </p>
                            <button type="button" wire:click="openGroupPicker" class="btn btn-primary btn-sm">
                                <i class="ri ri-add-large-line me-1"></i>
                                Add Section
                            </button>
                        </div>
                    </div>
                @else
                    @foreach ($assessmentGroups as $agIndex => $ag)
                        <div class="card shadow-sm border-0 mb-4"
                             wire:key="ag-{{ $agIndex }}-{{ $ag['question_group_id'] }}">

                            {{-- Group Card Header --}}
                            <div class="card-header py-3 border-bottom"
                                 style="background:linear-gradient(135deg,#D9D979,#FFFFEE);">

                                <div
                                    class="d-flex align-items-start
                                            justify-content-between gap-3">
                                    <div>
                                        <div
                                            class="d-flex align-items-center
                                                    gap-2 flex-wrap mb-1">
                                            <span class="badge bg-primary fw-semibold">
                                                Group {{ $loop->iteration }}
                                            </span>
                                            <span class="fw-semibold text-dark">
                                                {{ $ag['group_title'] }}
                                            </span>
                                            @if ($ag['questions_category'] === 'multiple')
                                                <span
                                                    class="badge bg-info-subtle text-info
                                                             border border-info-subtle">
                                                    <i class="ri ri-file-copy-2-line me-1"></i>
                                                    Passage
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary
                                                             border border-secondary-subtle">
                                                    Single
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            <code>{{ $ag['group_code'] }}</code> •
                                            {{ count($ag['questions']) }} question(s)
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        {{-- Add More Questions button --}}

                                        <button type="button"
                                                wire:click="removeAssessmentGroup({{ $agIndex }})"
                                                wire:confirm="Remove this group from the assessment?"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="ri ri-delete-bin-line me-1"></i>
                                            Remove Group
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0">

                                {{-- Passage for MULTIPLE --}}
                                @if ($ag['questions_category'] === 'multiple' && !empty($ag['group_content']['content']))
                                    @php
                                        $defaultLang = array_key_first($languages);
                                        $passageContent = $ag['group_content']['content'][$defaultLang] ?? '';
                                    @endphp
                                    @if ($passageContent)
                                        <div class="p-3 border-bottom bg-info-subtle">
                                            <p class="small fw-semibold text-info mb-2">
                                                <i class="ri ri-file-text-line me-1"></i>
                                                Group Passage
                                                <span class="badge bg-info text-white ms-1">
                                                    Shown above questions
                                                </span>
                                            </p>
                                            <div class="bg-white rounded p-3 small border">
                                                {!! $passageContent !!}
                                            </div>
                                        </div>
                                    @endif


                                    {{-- Group Settings --}}
                                    <div class="p-4 border-bottom bg-light">
                                        <div class="row g-3">

                                            <div class="col-12">
                                                <label class="form-label fw-medium small">
                                                    Group Instructions
                                                    <span class="text-muted fw-normal">(Optional)</span>
                                                </label>
                                                <textarea wire:model="assessmentGroups.{{ $agIndex }}.instructions" class="form-control form-control-sm"
                                                          rows="2" placeholder="Instructions shown before this group...">
                                            </textarea>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-medium small">
                                                    <i class="ri ri-timer-line me-1"></i>
                                                    Group Timer (sec)
                                                </label>
                                                <input type="number"
                                                       wire:model="assessmentGroups.{{ $agIndex }}.group_timer"
                                                       class="form-control form-control-sm" min="0"
                                                       placeholder="0 = no timer">
                                            </div>

                                            <div class="col-md-8">
                                                <label class="form-label fw-medium small d-block">
                                                    Settings
                                                </label>
                                                <div class="d-flex flex-wrap gap-4 mt-1">

                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               wire:model="assessmentGroups.{{ $agIndex }}.suffle_question"
                                                               id="shuffle-{{ $agIndex }}" role="switch">
                                                        <label class="form-check-label small"
                                                               for="shuffle-{{ $agIndex }}">
                                                            Shuffle Questions
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               wire:model="assessmentGroups.{{ $agIndex }}.allow_back_to_group_question"
                                                               id="backGroup-{{ $agIndex }}" role="switch">
                                                        <label class="form-check-label small"
                                                               for="backGroup-{{ $agIndex }}">
                                                            Allow Back to group Question
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               wire:model="assessmentGroups.{{ $agIndex }}.allow_back_to_previous_question"
                                                               id="backQ-{{ $agIndex }}" role="switch">
                                                        <label class="form-check-label small"
                                                               for="backQ-{{ $agIndex }}">
                                                            Allow Back (Question)
                                                        </label>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                                {{-- Questions List --}}
                                <div class="p-4">

                                    @if (empty($ag['questions']))
                                        <div class="text-center py-3 text-muted">
                                            <i class="ri ri-question-line fs-3 opacity-50"></i>
                                            <p class="small mt-2 mb-0">
                                                No questions added yet.
                                                Click "Add Questions" above.
                                            </p>
                                        </div>
                                    @else
                                        @foreach ($ag['questions'] as $qIndex => $question)
                                            <div class="border rounded-3 mb-3 overflow-hidden"
                                                 wire:key="aq-{{ $agIndex }}-{{ $qIndex }}-{{ $question['question_id'] }}">

                                                {{-- Question Row Header --}}
                                                <div
                                                    class="d-flex align-items-start
                                                            gap-3 p-3 bg-light border-bottom">

                                                    <span
                                                        class="badge bg-secondary fw-bold
                                                                 d-inline-flex align-items-center
                                                                 justify-content-center flex-shrink-0"
                                                        style="width:30px;height:30px;
                                                                 font-size:.85rem;">
                                                        {{ $qIndex + 1 }}
                                                    </span>

                                                    <div class="flex-1">
                                                        <p class="mb-1 small fw-medium text-dark">
                                                            {{ Str::limit($question['stem_en'] ?: '(No English stem)', 80) }}
                                                        </p>
                                                        <div class="d-flex flex-wrap gap-2">

                                                            @if ($question['answer_category'] === 'single_optional')
                                                                <span class="badge bg-primary-subtle text-primary"
                                                                      style="font-size:.7rem;">Single</span>
                                                            @elseif ($question['answer_category'] === 'multi_optional')
                                                                <span class="badge bg-info-subtle text-info"
                                                                      style="font-size:.7rem;">Multi</span>
                                                            @else
                                                                <span class="badge bg-secondary-subtle text-secondary"
                                                                      style="font-size:.7rem;">Open</span>
                                                            @endif

                                                            <span class="badge bg-success-subtle text-success"
                                                                  style="font-size:.7rem;">
                                                                {{ $question['marks'] }} Mark(s)
                                                            </span>

                                                            <code style="font-size:.7rem;">
                                                                {{ $question['question_code'] }}
                                                            </code>

                                                        </div>
                                                    </div>

                                                    <button type="button"
                                                            wire:click="removeQuestionFromGroup({{ $agIndex }}, {{ $qIndex }})"
                                                            wire:confirm="Remove this question?"
                                                            class="btn btn-sm btn-outline-danger flex-shrink-0">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>

                                                {{-- Question Settings Row --}}
                                                <div class="p-3 bg-white">
                                                    <div class="row g-2 align-items-end">

                                                        {{-- Question Type — MANDATORY --}}
                                                        <div class="col-md-5">
                                                            <label class="form-label fw-medium mb-1"
                                                                   style="font-size:.75rem;">
                                                                <i class="ri ri-list-check-2 me-1 text-primary"></i>
                                                                Question Type
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                wire:model="assessmentGroups.{{ $agIndex }}.questions.{{ $qIndex }}.question_type_id"
                                                                class="form-select form-select-sm
                                                                       @error("assessmentGroups.{$agIndex}.questions.{$qIndex}.question_type_id")
                                                                           is-invalid border-danger
                                                                       @enderror">
                                                                <option value="">— Select Type —</option>
                                                                @foreach ($questionTypes as $qt)
                                                                    <option value="{{ $qt['id'] }}">
                                                                        {{ $qt['name'] }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error("assessmentGroups.{$agIndex}.questions.{$qIndex}.question_type_id")
                                                            <div class="invalid-feedback" style="font-size:.7rem;">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>

                                                        {{-- Question Timer --}}
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-medium mb-1"
                                                                   style="font-size:.75rem;">
                                                                <i class="ri ri-timer-line me-1"></i>
                                                                Timer (sec)
                                                            </label>
                                                            <input type="number"
                                                                   wire:model="assessmentGroups.{{ $agIndex }}.questions.{{ $qIndex }}.question_timer"
                                                                   class="form-control form-control-sm" min="0"
                                                                   placeholder="0">
                                                        </div>

                                                        {{-- Negative Mark --}}
                                                        @if ($has_negative_mark)
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-medium mb-1"
                                                                       style="font-size:.75rem;">
                                                                    <i
                                                                        class="ri ri-subtract-line me-1 text-danger"></i>
                                                                    Negative Mark
                                                                </label>
                                                                <input type="number"
                                                                       wire:model="assessmentGroups.{{ $agIndex }}.questions.{{ $qIndex }}.negative_mark"
                                                                       class="form-control form-control-sm border-danger"
                                                                       min="0" step="0.5" placeholder="0">
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                                {{-- /questions --}}

                            </div>
                       <div class="card-footer p-2">
                           <button type="button" wire:click="openAddMoreQuestions({{ $agIndex }})"
                                   class="btn btn-sm btn-outline-success">
                               <i class="ri ri-add-large-line me-1"></i>
                               Add More Questions to this section
                           </button>
                       </div>
                        </div>
                    @endforeach

                    <div class="text-center mb-4">
                        <button type="button" wire:click="openGroupPicker"
                                class="btn btn-outline-primary btn-sm px-4">
                            <i class="ri ri-add-large-line me-1"></i>
                            Add Another Section
                        </button>
                    </div>

                @endif

            </div>
            {{-- /left --}}

            {{-- Right — Summary --}}
            <div class="col-lg-4">

                {{-- Assessment Info Card --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div
                        class="card-header bg-white border-bottom py-3
                                d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-draft-line text-primary me-2"></i>
                            Assessment Info
                        </h6>
                        <button type="button" wire:click="backToForm" class="btn btn-sm btn-outline-primary">
                            <i class="ri ri-pencil-line me-1"></i>Edit
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Title</span>
                                </div>
                                <p class="mb-0 small fw-semibold text-dark mt-1">
                                    {{ $title ?: '—' }}
                                </p>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Code</span>
                                <code class="small">{{ $assessment_code }}</code>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Type</span>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ strtoupper($assessment_type_id ?: '—') }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Status</span>
                                <span
                                    class="badge {{ $status === 'publish' ? 'bg-success' : ($status === 'draft' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Duration</span>
                                <span class="small fw-medium">
                                    {{ $duration_minutes ? $duration_minutes . ' min' : 'Per group/question' }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Negative Mark</span>
                                <span
                                    class="badge {{ $has_negative_mark ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $has_negative_mark ? 'Enabled' : 'Disabled' }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex
           justify-content-between align-items-center">
                                <span class="small text-muted">Max Attempts</span>
                                <span class="badge bg-primary-subtle text-primary">
        {{ $max_attempts }}x
    </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
           justify-content-between align-items-center">
                                <span class="small text-muted">Shuffle Sections</span>
                                <span class="badge {{ $shuffle_sections
        ? 'bg-success-subtle text-success'
        : 'bg-secondary-subtle text-secondary' }}">
        {{ $shuffle_sections ? 'Yes' : 'No' }}
    </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
           justify-content-between align-items-center">
                                <span class="small text-muted">Show Result</span>
                                <span class="badge {{ $show_result_immediately
        ? 'bg-success-subtle text-success'
        : 'bg-secondary-subtle text-secondary' }}">
        {{ $show_result_immediately ? 'Immediately' : 'Later' }}
    </span>
                            </li>

                            @if ($show_result_immediately)
                                <li class="list-group-item px-0 d-flex
               justify-content-between align-items-center">
                                    <span class="small text-muted">Show Answers</span>
                                    <span class="badge {{ $show_correct_answers
            ? 'bg-success-subtle text-success'
            : 'bg-secondary-subtle text-secondary' }}">
            {{ $show_correct_answers ? 'Yes' : 'No' }}
        </span>
                                </li>

                                @if ($show_correct_answers)
                                    <li class="list-group-item px-0 d-flex
                   justify-content-between align-items-center">
                                        <span class="small text-muted">Show Explanations</span>
                                        <span class="badge {{ $show_explainations
                ? 'bg-info-subtle text-info'
                : 'bg-secondary-subtle text-secondary' }}">
                {{ $show_explainations ? 'Yes' : 'No' }}
            </span>
                                    </li>
                                @endif
                            @endif
                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Passing Marks</span>
                                <span class="badge bg-warning text-dark">
                                    {{ $passing_marks }}
                                </span>
                            </li>

                            @if ($instructions)
                                <li class="list-group-item px-0">
                                    <span class="small text-muted d-block mb-1">
                                        Instructions
                                    </span>
                                    <p class="small mb-0 text-dark">
                                        {{ Str::limit($instructions, 100) }}
                                    </p>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>

                {{-- Summary Stats --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-bar-chart-box-line text-info me-2"></i>
                            Summary
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Total Groups</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ count($assessmentGroups) }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Total Questions</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ collect($assessmentGroups)->sum(fn($ag) => count($ag['questions'])) }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Total Marks</span>
                                <span class="badge bg-success rounded-pill">
                                    {{ $total_marks }}
                                </span>
                            </li>

                            <li
                                class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Passing Marks</span>
                                <span class="badge bg-warning text-dark rounded-pill">
                                    {{ $passing_marks }}
                                </span>
                            </li>

                        </ul>
                    </div>
                    <div class="card-footer bg-white p-3">
                        <button type="button" wire:click="saveBuilder" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">
                            <span wire:loading wire:target="saveBuilder">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Saving…
                            </span>
                            <span wire:loading.remove wire:target="saveBuilder">
                                <i class="ri ri-save-line me-1"></i>
                                Save Builder
                            </span>
                        </button>
                    </div>
                </div>

            </div>
            {{-- /right --}}

        </div>


        {{-- ══════════════════════════════════════════════════════════
             QUESTION GROUP PICKER MODAL
        ══════════════════════════════════════════════════════════ --}}
        @if ($showGroupPicker)

            {{-- Backdrop --}}
            <div class="modal-backdrop fade show" style="z-index:1040;"></div>

            {{-- Modal --}}
            <div class="modal fade show d-block" style="z-index:1050;" tabindex="-1">

                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                        {{-- ── Modal Header ──────────────────────────────── --}}
                        <div class="modal-header border-bottom py-3"
                             style="background:linear-gradient(135deg,#f8f9ff,#eef1ff);">

                            <div>
                                <h6 class="modal-title fw-bold mb-0">
                                    @if ($pickerMode === 'existing')
                                        <i class="ri ri-add-circle-line text-success me-2"></i>
                                        Add More Questions
                                    @else
                                        <i class="ri ri-layout-grid-line text-primary me-2"></i>
                                        Select Question Group & Questions
                                    @endif
                                </h6>

                                @if ($pickerMode === 'existing' && $pickerAgIndex !== null)
                                    <p class="mb-0 mt-1 small text-muted">
                                        Group:
                                        <strong>
                                            {{ $assessmentGroups[$pickerAgIndex]['group_title'] }}
                                        </strong>
                                        <code class="ms-1">
                                            {{ $assessmentGroups[$pickerAgIndex]['group_code'] }}
                                        </code>
                                    </p>
                                @else
                                    <p class="mb-0 mt-1 small text-muted">
                                        Select a group from the left, then pick questions.
                                    </p>
                                @endif
                            </div>

                            <button type="button" wire:click="closeGroupPicker" class="btn-close ms-3"></button>
                        </div>

                        {{-- ── Modal Body ─────────────────────────────────── --}}
                        <div class="modal-body p-0" style="max-height:65vh;overflow:hidden;">

                            {{-- TWO PANEL LAYOUT --}}
                            <div class="d-flex h-100" style="min-height:400px;">

                                {{-- LEFT PANEL — Group List (new mode only) --}}
                                @if ($pickerMode === 'new')

                                    <div class="border-end bg-light flex-shrink-0"
                                         style="width:220px;overflow-y:auto;">

                                        {{-- Search --}}
                                        <div class="p-2 border-bottom sticky-top bg-light">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white">
                                                    <i class="ri ri-search-line text-muted"
                                                       style="font-size:.8rem;"></i>
                                                </span>
                                                <input type="text"
                                                       wire:model.live.debounce.300ms="groupPickerSearch"
                                                       class="form-control form-control-sm" placeholder="Search...">
                                            </div>
                                        </div>

                                        {{-- Group items --}}
                                        <div class="list-group list-group-flush">

                                            @forelse ($pickerGroups as $pg)
                                                @php
                                                    $pgContent = $pg->group_content ?? [];
                                                    $pgTitle =
                                                        $pgContent['title'][array_key_first($languages)] ??
                                                        $pg->group_code;
                                                    $isMultiUsed =
                                                        $pg->questions_category === 'multiple' &&
                                                        collect($assessmentGroups)
                                                            ->where('question_group_id', $pg->id)
                                                            ->count() > 0;
                                                @endphp

                                                <button type="button"
                                                        wire:click="selectPickerGroup({{ $pg->id }})"
                                                        wire:key="pg-{{ $pg->id }}"
                                                        class="list-group-item list-group-item-action
                                                       px-3 py-2 border-0
                                                       {{ $pickerGroupId === $pg->id ? 'active' : '' }}
                                                       {{ $isMultiUsed ? 'disabled opacity-50' : '' }}"
                                                        style="font-size:.82rem;">

                                                    <div class="fw-semibold text-truncate mb-1
                                                        {{ $pickerGroupId === $pg->id ? 'text-white' : 'text-dark' }}"
                                                         title="{{ $pgTitle }}">
                                                        {{ $pgTitle }}
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center
                                                        gap-1 flex-wrap">

                                                        {{-- Category --}}
                                                        <span
                                                            class="badge
                                                    {{ $pg->questions_category === 'multiple' ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}"
                                                            style="font-size:.6rem;">
                                                            {{ ucfirst($pg->questions_category) }}
                                                        </span>

                                                        {{-- Question count --}}
                                                        <span class="badge bg-light text-dark border"
                                                              style="font-size:.6rem;">
                                                            {{ $pg->questions_count }}Q
                                                        </span>

                                                        {{-- Used lock --}}
                                                        @if ($isMultiUsed)
                                                            <span class="badge bg-warning-subtle text-warning"
                                                                  style="font-size:.6rem;">
                                                                <i class="ri ri-lock-line"></i>
                                                            </span>
                                                        @endif

                                                    </div>
                                                </button>

                                            @empty
                                                <div class="text-center py-4 text-muted px-2">
                                                    <i class="ri ri-folder-open-line fs-4 opacity-50"></i>
                                                    <p class="small mt-2 mb-0">No groups</p>
                                                </div>
                                            @endforelse

                                        </div>
                                    </div>

                                @endif
                                {{-- /left panel --}}


                                {{-- RIGHT PANEL — Questions --}}
                                <div class="flex-1 overflow-y-auto" style="overflow-y:auto;min-width:0;">

                                    @if (!$pickerGroupId)

                                        {{-- Empty state --}}
                                        <div
                                            class="d-flex flex-column align-items-center
                                            justify-content-center h-100 text-muted py-5">
                                            <i class="ri ri-arrow-left-line fs-1 opacity-25"></i>
                                            <p class="small mt-3 mb-0">
                                                Select a group to view questions.
                                            </p>
                                        </div>
                                    @else
                                        {{-- Passage block for multiple --}}
                                        @php
                                            if ($pickerMode === 'new') {
                                                $selGroup = $pickerGroups->firstWhere('id', $pickerGroupId);
                                                $selContent = $selGroup?->group_content ?? [];
                                                $isPassageMode = $selGroup?->questions_category === 'multiple';
                                            } else {
                                                $selContent = $assessmentGroups[$pickerAgIndex]['group_content'] ?? [];
                                                $isPassageMode =
                                                    $assessmentGroups[$pickerAgIndex]['questions_category'] ===
                                                    'multiple';
                                            }
                                            $passageEn = $selContent['content'][array_key_first($languages)] ?? '';
                                        @endphp

                                        @if ($isPassageMode && $passageEn)
                                            <div class="p-3 border-bottom bg-info-subtle">
                                                <p class="small fw-semibold text-info mb-2">
                                                    <i class="ri ri-file-text-line me-1"></i>
                                                    Group Passage
                                                </p>
                                                <div class="bg-white rounded p-2 small border"
                                                     style="max-height:100px;overflow-y:auto;">
                                                    {!! $passageEn !!}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Questions list --}}
                                        <div class="p-3">

                                            {{-- Header row --}}
                                            <div
                                                class="d-flex align-items-center
                                                justify-content-between mb-2">
                                                <span class="small fw-semibold text-dark">
                                                    @if ($pickerMode === 'existing')
                                                        Add More Questions
                                                    @else
                                                        Select Questions
                                                    @endif
                                                </span>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ count($pickerSelectedQIds) }} selected
                                                </span>
                                            </div>

                                            {{-- Question items --}}
                                            @forelse ($pickerQuestions as $pq)
                                                <label wire:key="pq-{{ $pq['id'] }}"
                                                       class="d-flex align-items-start gap-2
                                                      p-2 rounded-3 mb-2 border
                                                      {{ $pq['already_used']
                                                          ? 'opacity-50 bg-light'
                                                          : (in_array($pq['id'], $pickerSelectedQIds)
                                                              ? 'bg-primary-subtle border-primary'
                                                              : 'bg-white') }}"
                                                       style="cursor:{{ $pq['already_used'] ? 'not-allowed' : 'pointer' }};">

                                                    <input type="checkbox" class="form-check-input mt-1 flex-shrink-0"
                                                           wire:click="togglePickerQuestion({{ $pq['id'] }})"
                                                        {{ in_array($pq['id'], $pickerSelectedQIds) ? 'checked' : '' }}
                                                        {{ $pq['already_used'] ? 'disabled' : '' }}>

                                                    <div class="flex-1 min-w-0">

                                                        <p class="mb-1 small fw-medium text-dark text-truncate"
                                                           title="{{ $pq['stem_en'] }}">
                                                            {{ Str::limit($pq['stem_en'] ?: '(No English stem)', 65) }}
                                                        </p>

                                                        <div class="d-flex flex-wrap gap-1 align-items-center">

                                                            {{-- Answer category --}}
                                                            @if ($pq['answer_category'] === 'single_optional')
                                                                <span class="badge bg-primary-subtle text-primary"
                                                                      style="font-size:.6rem;">Single</span>
                                                            @elseif ($pq['answer_category'] === 'multi_optional')
                                                                <span class="badge bg-info-subtle text-info"
                                                                      style="font-size:.6rem;">Multi</span>
                                                            @else
                                                                <span class="badge bg-secondary-subtle text-secondary"
                                                                      style="font-size:.6rem;">Open</span>
                                                            @endif

                                                            {{-- Marks --}}
                                                            <span class="badge bg-success-subtle text-success"
                                                                  style="font-size:.6rem;">
                                                                {{ $pq['marks'] }}M
                                                            </span>

                                                            {{-- Code --}}
                                                            <code style="font-size:.6rem;color:#6c757d;">
                                                                {{ $pq['question_code'] }}
                                                            </code>

                                                            {{-- Already used --}}
                                                            @if ($pq['already_used'])
                                                                <span class="badge bg-warning-subtle text-warning"
                                                                      style="font-size:.6rem;">
                                                                    <i class="ri ri-lock-line"></i>
                                                                    Added
                                                                </span>
                                                            @endif

                                                        </div>
                                                    </div>

                                                </label>

                                            @empty
                                                <div class="text-center py-4 text-muted">
                                                    <i class="ri ri-question-line fs-3 opacity-30"></i>
                                                    <p class="small mt-2 mb-0">
                                                        No questions in this group.
                                                    </p>
                                                </div>
                                            @endforelse

                                        </div>

                                    @endif

                                </div>
                                {{-- /right panel --}}

                            </div>
                            {{-- /two panel layout --}}

                        </div>
                        {{-- /modal body --}}


                        {{-- ── Modal Footer ──────────────────────────────── --}}
                        <div class="modal-footer border-top py-2">

                            <div class="me-auto">
                                @if ($pickerGroupId && count($pickerSelectedQIds) > 0)
                                    <span class="small text-success">
                                        <i class="ri ri-checkbox-circle-line me-1"></i>
                                        <strong>{{ count($pickerSelectedQIds) }}</strong>
                                        question(s) selected
                                    </span>
                                @else
                                    <span class="small text-muted">
                                        Select at least one question to continue.
                                    </span>
                                @endif
                            </div>

                            <button type="button" wire:click="closeGroupPicker"
                                    class="btn btn-outline-secondary btn-sm">
                                <i class="ri ri-close-line me-1"></i>
                                Cancel
                            </button>

                            <button type="button" wire:click="addGroupToAssessment" wire:loading.attr="disabled"
                                    class="btn btn-primary btn-sm" @if (!$pickerGroupId || empty($pickerSelectedQIds)) disabled @endif>
                                <span wire:loading wire:target="addGroupToAssessment">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                </span>
                                <span wire:loading.remove wire:target="addGroupToAssessment">
                                    <i class="ri ri-add-large-line me-1"></i>
                                </span>
                                @if ($pickerMode === 'existing')
                                    Add Selected Questions
                                @else
                                    Add to Assessment
                                @endif
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        @endif
        {{-- /picker modal --}}

    @endif
    {{-- /builder --}}

</div>
