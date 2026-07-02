<div class="assessment-list-wrapper">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="ri ri-checkbox-circle-line fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
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

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-draft-line text-primary me-2"></i>
                Assessments
            </h4>
            <p class="text-muted small mb-0">Manage all assessments and their Sections.</p>
        </div>
        @can('system.assessment.view')
            <a href="{{ route('admin.assessment-masters.manage') }}"
               class="btn btn-primary btn-sm shadow-sm">
                <i class="ri ri-add-large-line me-1"></i>
                New Assessment
            </a>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-2">
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        @foreach ($assessmentTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
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
                    <div class="px-4 py-3">
                        <div class="d-flex align-items-start justify-content-between gap-3">

                            {{-- Left --}}
                            <div class="flex-1">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="fw-semibold text-dark fs-6">{{ $assessment->title }}</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ strtoupper($assessment->assessment_type_id) }}
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        Age Group-{{ $assessment->ageGroup->name }}
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        Level-{{ $assessment->difficultyLevel->level }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 mb-3">
                                    <small class="text-muted">
                                        <i class="ri ri-barcode-line me-1"></i>{{ $assessment->assessment_code }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="ri ri-question-line me-1"></i>{{ $assessment->assessment_questions_count }} Question(s)
                                    </small>
                                    <small class="text-muted">
                                        <i class="ri ri-trophy-line me-1"></i>{{ $assessment->total_marks }} Marks
                                    </small>
                                    <small class="text-muted">
                                        <i class="ri ri-crosshair-line me-1"></i>Pass: {{ $assessment->passing_marks }}
                                    </small>
                                    @if ($assessment->duration_minutes)
                                        <small class="text-muted">
                                            <i class="ri ri-timer-line me-1"></i>{{ $assessment->duration_minutes }} min
                                        </small>
                                    @endif
                                    <small class="text-muted">
                                        <i class="ri ri-user-line me-1"></i>{{ $assessment->ageGroup?->name ?? '—' }} years
                                    </small>
                                </div>

                                {{-- Settings Badges --}}
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle"
                                          data-bs-toggle="tooltip" title="Max Attempts">
                                        <i class="ri ri-repeat-line me-1"></i>{{ $assessment->max_attempts ?? 1 }}x Attempt
                                    </span>

                                    @if ($assessment->has_negative_mark)
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle"
                                              data-bs-toggle="tooltip" title="Negative Marking Enabled">
                                            <i class="ri ri-subtract-line me-1"></i>Negative Mark
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle"
                                              data-bs-toggle="tooltip" title="No Negative Marking">
                                            <i class="ri ri-subtract-line me-1"></i>No Negative
                                        </span>
                                    @endif

                                    <span class="badge rounded-pill {{ $assessment->shuffle_sections ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                          data-bs-toggle="tooltip" title="Shuffle Sections">
                                        <i class="ri ri-shuffle-line me-1"></i>{{ $assessment->shuffle_sections ? 'Shuffle On' : 'Shuffle Off' }}
                                    </span>

                                    <span class="badge rounded-pill {{ $assessment->show_result_immediately ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                          data-bs-toggle="tooltip" title="Show Result Immediately">
                                        <i class="ri ri-bar-chart-line me-1"></i>{{ $assessment->show_result_immediately ? 'Instant Result' : 'Result Later' }}
                                    </span>

                                    @if ($assessment->show_result_immediately)
                                        <span class="badge rounded-pill {{ $assessment->show_correct_answers ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                              data-bs-toggle="tooltip" title="Show Correct Answers">
                                            <i class="ri ri-checkbox-circle-line me-1"></i>{{ $assessment->show_correct_answers ? 'Answers Shown' : 'Answers Hidden' }}
                                        </span>

                                        @if ($assessment->show_correct_answers)
                                            <span class="badge rounded-pill {{ $assessment->show_explainations ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}"
                                                  data-bs-toggle="tooltip" title="Show Explanations">
                                                <i class="ri ri-book-open-line me-1"></i>{{ $assessment->show_explainations ? 'Explanations On' : 'Explanations Off' }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="d-flex gap-2 flex-shrink-0">

                                @if ($assessment->attempts_count > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle d-flex align-items-center gap-1"
                                          data-bs-toggle="tooltip"
                                          title="{{ $assessment->attempts_count }} attempt(s) made. Edit & Builder are locked.">
                                        <i class="ri ri-lock-line"></i>
                                        {{ $assessment->attempts_count }} Attempts
                                    </span>
                                @endif

                                    <a href="{{ route('admin.assessments.preview', encrypt($assessment->id)) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="ri ri-eye-line"></i>
                                    </a>

                                {{-- Builder --}}
                                @if ($assessment->attempts_count > 0)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            disabled
                                            data-bs-toggle="tooltip"
                                            title="Locked: Students have attempted this assessment">
                                        <i class="ri ri-lock-line me-1"></i>Builder
                                    </button>
                                @else
                                    <a href="{{ route('admin.assessment-masters.build', encrypt($assessment->id)) }}"

                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="Open Builder">
                                        <i class="ri ri-tools-line me-1"></i>Builder
                                    </a>
                                @endif

                                {{-- Edit --}}
                                @if ($assessment->attempts_count > 0)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            disabled
                                            data-bs-toggle="tooltip"
                                            title="Locked: Cannot edit after students have attempted">
                                        <i class="ri ri-lock-line"></i>
                                    </button>
                                @else
                                    <a href="{{ route('admin.assessment-masters.manage', encrypt($assessment->id)) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Edit Assessment">
                                        <i class="ri ri-pencil-line"></i>
                                    </a>
                                @endif

                                {{-- Status Toggle --}}
                                <div class="btn-group btn-group-sm" role="group">
                                    @php
                                        $statusOptions = [
                                            'draft'     => ['warning',   'ri-draft-line',           'Draft'],
                                            'publish'   => ['success',   'ri-checkbox-circle-line', 'Live'],
                                            'unpublish' => ['secondary', 'ri-eye-off-line',         'Off'],
                                        ];
                                        $isLocked = $assessment->attempts_count > 0;
                                    @endphp

                                    @foreach ($statusOptions as $val => [$color, $icon, $label])
                                        @php $isDisabled = $isLocked && $val === 'draft'; @endphp
                                        <input type="radio"
                                               class="btn-check"
                                               name="status-{{ $assessment->id }}"
                                               id="st-{{ $assessment->id }}-{{ $val }}"
                                               value="{{ $val }}"
                                               autocomplete="off"
                                               @if(!$isDisabled) wire:click="changeStatus({{ $assessment->id }}, '{{ $val }}')" @endif
                                            {{ $assessment->status === $val ? 'checked' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                        <label class="btn btn-outline-{{ $color }} {{ $isDisabled ? 'opacity-50' : '' }}"
                                               for="st-{{ $assessment->id }}-{{ $val }}"
                                               data-bs-toggle="tooltip"
                                               title="{{ $isDisabled ? 'Cannot set to Draft after attempts made' : $label }}"
                                               style="font-size:.7rem;padding:.2rem .5rem;">
                                            <i class="ri {{ $icon }}"></i> {{ $label }}
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Delete --}}
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
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="ri ri-draft-line" style="font-size:3rem;opacity:.3;"></i>
                    <h6 class="mt-3 fw-semibold">No Assessments Found</h6>
                    <p class="small mb-4">Click "New Assessment" to get started.</p>
                    @can('system.assessment.view')
                        <a href="{{ route('admin.assessment-masters.manage') }}"
                           class="btn btn-primary btn-sm">
                            <i class="ri ri-add-large-line me-1"></i>New Assessment
                        </a>
                    @endcan
                </div>
            @endforelse

        </div>
    </div>

    <div class="mt-4">
        {{ $assessments->links('pagination::bootstrap-5') }}
    </div>

</div>
