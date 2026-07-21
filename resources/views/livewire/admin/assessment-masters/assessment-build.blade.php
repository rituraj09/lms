@php
    use App\Helper\Globals;
    $languages = Globals::LANGUAGES;
@endphp

<div class="assessment-build-wrapper">

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


    {{-- ══════════════════════════════════════════════════════════════
         VIEW: BUILDER
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'builder')

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-tools-line text-primary me-2"></i>Assessment Builder
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.assessment-masters.list') }}">Assessments</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.assessment-masters.manage', encrypt($assessmentId)) }}">
                                {{ $title }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Builder</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.assessment-masters.list') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Cancel
                </a>
                <button type="button" wire:click="backToForm" class="btn btn-outline-warning btn-sm">
                    <i class="ri ri-pencil-line me-1"></i>Edit Info
                </button>

                <button type="button" wire:click="saveBuilder" wire:loading.attr="disabled"
                    class="btn btn-primary btn-sm shadow-sm">
                    <span wire:loading wire:target="saveBuilder">
                        <span class="spinner-border spinner-border-sm me-1"></span>Saving…
                    </span>
                    <span wire:loading.remove wire:target="saveBuilder">
                        <i class="ri ri-save-line me-1"></i>Save Builder
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
                            <p class="small mb-4">Click "Add Section" to start building.</p>
                            <button type="button" wire:click="openGroupPicker" class="btn btn-primary btn-sm">
                                <i class="ri ri-add-large-line me-1"></i>Add Section
                            </button>
                        </div>
                    </div>
                @else
                    @foreach ($assessmentGroups as $agIndex => $ag)
                        <div class="card shadow-sm border-0 mb-4"
                            wire:key="ag-{{ $agIndex }}-{{ $ag['question_group_id'] }}">

                            {{-- Group Header --}}
                            <div class="card-header py-3 border-bottom"
                                style="background:linear-gradient(135deg,#D9D979,#FFFFEE);">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <span class="badge bg-primary fw-semibold">Group
                                                {{ $loop->iteration }}</span>
                                            <span class="fw-semibold text-dark">{{ $ag['group_title'] }}</span>
                                            @if ($ag['questions_category'] === 'multiple')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                    <i class="ri ri-file-copy-2-line me-1"></i>Passage
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
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
                                        <button type="button" wire:click="removeAssessmentGroup({{ $agIndex }})"
                                            wire:confirm="Remove this group from the assessment?"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="ri ri-delete-bin-line me-1"></i>Remove Group
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
                                                <i class="ri ri-file-text-line me-1"></i>Group Passage
                                                <span class="badge bg-info text-white ms-1">Shown above questions</span>
                                            </p>
                                            <div class="bg-white rounded p-3 small border">{!! $passageContent !!}</div>
                                        </div>
                                    @endif

                                    {{-- Group Settings --}}
                                    <div class="p-4 border-bottom bg-light">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-medium small">
                                                    Group Instructions <span
                                                        class="text-muted fw-normal">(Optional)</span>
                                                </label>
                                                <textarea wire:model="assessmentGroups.{{ $agIndex }}.instructions" class="form-control form-control-sm"
                                                    rows="2" placeholder="Instructions shown before this group..."></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium small">
                                                    <i class="ri ri-timer-line me-1"></i>Group Timer (sec)
                                                </label>
                                                <input type="number"
                                                    wire:model="assessmentGroups.{{ $agIndex }}.group_timer"
                                                    class="form-control form-control-sm" min="0"
                                                    placeholder="0 = no timer">
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-medium small d-block">Settings</label>
                                                <div class="d-flex flex-wrap gap-4 mt-1">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch"
                                                            wire:model="assessmentGroups.{{ $agIndex }}.suffle_question"
                                                            id="shuffle-{{ $agIndex }}">
                                                        <label class="form-check-label small"
                                                            for="shuffle-{{ $agIndex }}">
                                                            Shuffle Questions
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch"
                                                            wire:model="assessmentGroups.{{ $agIndex }}.allow_back_to_group_question"
                                                            id="backGroup-{{ $agIndex }}">
                                                        <label class="form-check-label small"
                                                            for="backGroup-{{ $agIndex }}">
                                                            Allow Back to group Question
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch"
                                                            wire:model="assessmentGroups.{{ $agIndex }}.allow_back_to_previous_question"
                                                            id="backQ-{{ $agIndex }}">
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
                                            <p class="small mt-2 mb-0">No questions added yet.</p>
                                        </div>
                                    @else
                                        @foreach ($ag['questions'] as $qIndex => $question)
                                            <div class="border rounded-3 mb-3 overflow-hidden"
                                                wire:key="aq-{{ $agIndex }}-{{ $qIndex }}-{{ $question['question_id'] }}">

                                                {{-- Question Header --}}
                                                <div class="d-flex align-items-start gap-3 p-3 bg-light border-bottom">
                                                    <span
                                                        class="badge bg-secondary fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                        style="width:30px;height:30px;font-size:.85rem;">
                                                        {{ $qIndex + 1 }}
                                                    </span>
                                                    <div class="flex-1">
                                                        <p class="mb-1 small fw-medium text-dark">
                                                            {!! Str::limit($question['stem_en'] ?: '(No English stem)', 80) !!}
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
                                                            <code
                                                                style="font-size:.7rem;">{{ $question['question_code'] }}</code>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        wire:click="removeQuestionFromGroup({{ $agIndex }}, {{ $qIndex }})"
                                                        wire:confirm="Remove this question?"
                                                        class="btn btn-sm btn-outline-danger flex-shrink-0">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>

                                                {{-- Question Settings --}}
                                                <div class="p-3 bg-white">
                                                    <div class="row g-2 align-items-end">



                                                        <div class="col-md-3">
                                                            <label class="form-label fw-medium mb-1"
                                                                style="font-size:.75rem;">
                                                                <i class="ri ri-timer-line me-1"></i>Timer (sec)
                                                            </label>
                                                            <input type="number"
                                                                wire:model="assessmentGroups.{{ $agIndex }}.questions.{{ $qIndex }}.question_timer"
                                                                class="form-control form-control-sm" min="0"
                                                                placeholder="0">
                                                        </div>

                                                        @if ($has_negative_mark)
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-medium mb-1"
                                                                    style="font-size:.75rem;">
                                                                    <i
                                                                        class="ri ri-subtract-line me-1 text-danger"></i>Negative
                                                                    Mark
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

                            </div>

                            <div class="card-footer p-2">
                                <button type="button" wire:click="openAddMoreQuestions({{ $agIndex }})"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="ri ri-add-large-line me-1"></i>Add More Questions to this section
                                </button>
                            </div>

                        </div>
                    @endforeach

                    <div class="text-center mb-4">
                        <button type="button" wire:click="openGroupPicker"
                            class="btn btn-outline-primary btn-sm px-4">
                            <i class="ri ri-add-large-line me-1"></i>Add Another Section
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
                        class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-draft-line text-primary me-2"></i>Assessment Info
                        </h6>
                        <button type="button" wire:click="backToForm" class="btn btn-sm btn-outline-primary">
                            <i class="ri ri-pencil-line me-1"></i>Edit
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0">
                                <span class="small text-muted">Title</span>
                                <p class="mb-0 small fw-semibold text-dark mt-1">{{ $title ?: '—' }}</p>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Code</span>
                                <code class="small">{{ $assessment_code }}</code>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Type</span>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ strtoupper($assessment_type_id ?: '—') }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Age Group</span>
                                <span class="badge bg-warning-subtle text-warning">
                                    {{ $ageGroup }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Difficulty level</span>
                                <span class="badge bg-danger-subtle text-danger">
                                    {{ $difficultLevel }}
                                </span>
                            </li>


                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Status</span>
                                <span
                                    class="badge {{ $status === 'publish' ? 'bg-success' : ($status === 'draft' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Duration</span>
                                <span class="small fw-medium">
                                    {{ $duration_minutes ? $duration_minutes . ' min' : 'Per group/question' }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Negative Mark</span>
                                <span
                                    class="badge {{ $has_negative_mark ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $has_negative_mark ? 'Enabled' : 'Disabled' }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Max Attempts</span>
                                <span class="badge bg-primary-subtle text-primary">{{ $max_attempts }}x</span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Shuffle Sections</span>
                                <span
                                    class="badge {{ $shuffle_sections ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $shuffle_sections ? 'Yes' : 'No' }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Show Result</span>
                                <span
                                    class="badge {{ $show_result_immediately ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $show_result_immediately ? 'Immediately' : 'Later' }}
                                </span>
                            </li>

                            @if ($show_result_immediately)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Show Answers</span>
                                    <span
                                        class="badge {{ $show_correct_answers ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $show_correct_answers ? 'Yes' : 'No' }}
                                    </span>
                                </li>

                                @if ($show_correct_answers)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Show Explanations</span>
                                        <span
                                            class="badge {{ $show_explainations ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $show_explainations ? 'Yes' : 'No' }}
                                        </span>
                                    </li>
                                @endif
                            @endif

                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Passing Marks</span>
                                <span class="badge bg-warning text-dark">{{ $passing_marks }}</span>
                            </li>

                            @if ($instructions)
                                <li class="list-group-item px-0">
                                    <span class="small text-muted d-block mb-1">Instructions</span>
                                    <p class="small mb-0 text-dark">{{ Str::limit($instructions, 100) }}</p>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>

                {{-- Summary Stats --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-bar-chart-box-line text-info me-2"></i>Summary
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Total Groups</span>
                                <span class="badge bg-primary rounded-pill">{{ count($assessmentGroups) }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Total Questions</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ collect($assessmentGroups)->sum(fn($ag) => count($ag['questions'])) }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Total Marks</span>
                                <span class="badge bg-success rounded-pill">{{ $total_marks }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Passing Marks</span>
                                <span class="badge bg-warning text-dark rounded-pill">{{ $passing_marks }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white p-3">
                        <button type="button" wire:click="saveBuilder" wire:loading.attr="disabled"
                            class="btn btn-primary w-100">
                            <span wire:loading wire:target="saveBuilder">
                                <span class="spinner-border spinner-border-sm me-1"></span>Saving…
                            </span>
                            <span wire:loading.remove wire:target="saveBuilder">
                                <i class="ri ri-save-line me-1"></i>Save Builder
                            </span>
                        </button>
                    </div>
                </div>

            </div>
            {{-- /right --}}

        </div>

    @endif
    {{-- /builder view --}}


    {{-- ══════════════════════════════════════════════════════════════
         VIEW: GROUP PICKER (FULL PAGE)
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'group-picker')

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    @if ($pickerMode === 'existing')
                        <i class="ri ri-add-circle-line text-success me-2"></i>Add More Questions
                    @else
                        <i class="ri ri-layout-grid-line text-primary me-2"></i>Select Question Group & Questions
                    @endif
                </h4>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.assessment-masters.list') }}">Assessments</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.assessment-masters.manage', encrypt($assessmentId)) }}">
                                {{ $title }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" wire:click.prevent="closeGroupPicker">Builder</a>
                        </li>
                        <li class="breadcrumb-item active">
                            @if ($pickerMode === 'existing' && $pickerAgIndex !== null)
                                Add to: {{ $assessmentGroups[$pickerAgIndex]['group_title'] }}
                            @else
                                Select Group
                            @endif
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <button type="button" wire:click="closeGroupPicker" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i>Back to Builder
                </button>

                @if ($pickerGroupId && count($pickerSelectedQIds) > 0)
                    <button type="button" wire:click="addGroupToAssessment" wire:loading.attr="disabled"
                        class="btn btn-primary btn-sm shadow-sm">
                        <span wire:loading wire:target="addGroupToAssessment">
                            <span class="spinner-border spinner-border-sm me-1"></span>Adding…
                        </span>
                        <span wire:loading.remove wire:target="addGroupToAssessment">
                            <i class="ri ri-add-large-line me-1"></i>
                            Add {{ count($pickerSelectedQIds) }} Question(s)
                        </span>
                    </button>
                @endif
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <span class="small badge bg-secondary-subtle text-dark text-small">
                    Code: {{ $assessment_code }}
                </span>
                Title: {{ $title }}
                <span class="small badge bg-primary-subtle text-primary text-small">
                    {{ strtoupper($assessment_type_id ?: '—') }}
                </span>
                <span class="small badge bg-warning-subtle text-warning  text-small">
                    {{ $ageGroup }}
                </span>
                <span class="small badge bg-danger-subtle text-danger  text-small">
                    {{ $difficultLevel }}
                </span>
            </div>

        </div>

        <div class="row g-4">

            {{-- Left Panel — Group List (new mode only) --}}
            @if ($pickerMode === 'new')
                <div class="col-lg-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark small">
                                <i class="ri ri-folder-line text-primary me-2"></i>Question Groups
                            </h6>
                        </div>
                        <div class="card-body p-0">

                            {{-- Search --}}
                            <div class="p-2 border-bottom bg-light">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white">
                                        <i class="ri ri-search-line text-muted"></i>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="groupPickerSearch"
                                        class="form-control" placeholder="Search groups...">
                                </div>
                            </div>

                            {{-- Groups List --}}
                            <div style="max-height:600px;overflow-y:auto;">
                                @forelse ($pickerGroups as $pg)
                                    @php
                                        $pgContent = $pg->group_content ?? [];
                                        $pgTitle = $pgContent['title'][array_key_first($languages)] ?? $pg->group_code;

                                        $usedCount = $pg->used_count ?? 0;
                                        $totalCount = $pg->total_count ?? 0;
                                        $someUsed = $usedCount > 0;
                                        $isSelected = $pickerGroupId === $pg->id;

                                        $borderStyle = match (true) {
                                            $someUsed => 'border-start border-warning border-3',
                                            $isSelected => 'border-start border-primary border-3',
                                            default => '',
                                        };
                                    @endphp

                                    <button type="button" wire:click="selectPickerGroup({{ $pg->id }})"
                                        wire:key="pg-{{ $pg->id }}"
                                        class="list-group-item list-group-item-action px-3 py-2 border-0
                   {{ $isSelected ? 'active' : '' }}
                   {{ $borderStyle }}"
                                        style="font-size:.85rem;">

                                        {{-- Group Title --}}
                                        <div class="fw-semibold text-truncate mb-1
                    {{ $isSelected ? 'text-warning' : 'text-dark' }}"
                                            title="{{ $pgTitle }}">
                                            {{ $pgTitle }}
                                        </div>

                                        {{-- Badges Row --}}
                                        <div class="d-flex align-items-center gap-1 flex-wrap">

                                            {{-- Category badge --}}
                                            <span
                                                class="badge {{ $pg->questions_category === 'multiple' ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}"
                                                style="font-size:.65rem;">
                                                {{ ucfirst($pg->questions_category) }}
                                            </span>

                                            {{-- Question count --}}
                                            <span class="badge bg-light text-dark border" style="font-size:.65rem;">
                                                {{ $pg->questions_count }}Q
                                            </span>

                                            {{-- Some (not all) questions used → partial indicator --}}
                                            @if ($someUsed)
                                                <span
                                                    class="badge bg-warning-subtle text-warning border border-warning-subtle"
                                                    style="font-size:.65rem;">
                                                    <i class="ri ri-information-line me-1"></i>
                                                    {{ $usedCount }}/{{ $totalCount }} Used
                                                </span>
                                            @endif

                                        </div>

                                    </button>

                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="ri ri-folder-open-line fs-3 opacity-30"></i>
                                        <p class="small mt-2 mb-0">No available groups found</p>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            {{-- Right Panel — Questions List --}}
            <div class="{{ $pickerMode === 'new' ? 'col-lg-9' : 'col-12' }}">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-question-line text-primary me-2"></i>
                                @if ($pickerMode === 'existing')
                                    Questions from: <span
                                        class="text-primary">{{ $assessmentGroups[$pickerAgIndex]['group_title'] }}</span>
                                @elseif ($pickerGroupId)
                                    @php
                                        $selGroup = $pickerGroups->firstWhere('id', $pickerGroupId);
                                        $selTitle =
                                            $selGroup?->group_content['title'][array_key_first($languages)] ??
                                            $selGroup?->group_code;
                                    @endphp
                                    Questions from: <span class="text-primary">{{ $selTitle }}</span>
                                @else
                                    Select a Group First
                                @endif
                            </h6>


                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class=" p-3 border-bottom bg-info-subtle ">
                            {{-- SELECT ALL checkbox (only when questions are available) --}}
                            @if ($pickerGroupId && $pickerQuestions && $pickerQuestions->count() > 0)
                                @php

                                    $selectableIds = $pickerQuestions
                                        ->filter(fn($pq) => !in_array($pq->id, $currentGroupQuestionIds))
                                        ->pluck('id')
                                        ->toArray();
                                    $allSelected =
                                        count($selectableIds) > 0 &&
                                        count(array_intersect($selectableIds, $pickerSelectedQIds)) ===
                                            count($selectableIds);
                                @endphp
                                @if (count($selectableIds) > 0)
                                    <input type="checkbox" class="form-check-input" id="selectAllQuestions"
                                        {{ $allSelected ? 'checked' : '' }}
                                        wire:click="{{ $allSelected ? 'deselectAllPickerQuestions' : 'selectAllPickerQuestions' }}"
                                        style="cursor:pointer; width:1.1rem; height:1.1rem;">
                                    <label class="form-check-label small fw-medium mb-0" for="selectAllQuestions"
                                        style="cursor:pointer;">
                                        {{ $allSelected ? 'Deselect All' : 'Select All' }}
                                        <span class="text-muted">({{ count($selectableIds) }})</span>
                                    </label>
                                @endif
                            @endif

                            {{-- Selected count badge --}}
                            @if ($pickerGroupId && count($pickerSelectedQIds) > 0)
                                <span class="badge bg-success rounded-pill">
                                    {{ count($pickerSelectedQIds) }} selected
                                </span>
                            @endif
                        </div>
                        @if (!$pickerGroupId)
                            <div class="text-center py-5 text-muted">
                                <i class="ri ri-arrow-left-line fs-1 opacity-25"></i>
                                <p class="mt-3 mb-0">Select a question group from the left panel</p>
                            </div>
                        @else
                            {{-- Passage Display (if exists) --}}
                            @php
                                if ($pickerMode === 'new') {
                                    $selGroup = $pickerGroups->firstWhere('id', $pickerGroupId);
                                    $selContent = $selGroup?->group_content ?? [];
                                    $isPassageMode = $selGroup?->questions_category === 'multiple';
                                } else {
                                    $selContent = $assessmentGroups[$pickerAgIndex]['group_content'] ?? [];
                                    $isPassageMode =
                                        $assessmentGroups[$pickerAgIndex]['questions_category'] === 'multiple';
                                }
                                $passageEn = $selContent['content'][array_key_first($languages)] ?? '';
                            @endphp

                            @if ($isPassageMode && $passageEn)
                                <div class="p-3 border-bottom bg-info-subtle">
                                    <p class="small fw-semibold text-info mb-2">
                                        <i class="ri ri-file-text-line me-1"></i>Group Passage
                                    </p>
                                    <div class="bg-white rounded p-3 small border"
                                        style="max-height:150px;overflow-y:auto;">
                                        {!! $passageEn !!}
                                    </div>
                                </div>
                            @endif

                            {{-- Questions (Paginated & Scrollable) --}}
                            <div class="p-3" style="max-height:600px;overflow-y:auto;">

                                @if ($pickerQuestions && $pickerQuestions->count() > 0)
                                    @foreach ($pickerQuestions as $pq)
                                        @php
                                            $content = $pq->question_content ?? [];
                                            $stemEn = $content['stem'][array_key_first($languages)] ?? '(No stem)';

                                            $alreadyInGroup = $pq->already_in_group ?? false;
                                            $isSelected = in_array($pq->id, $pickerSelectedQIds);

                                            $rowClass = match (true) {
                                                $alreadyInGroup => 'bg-secondary-subtle border-secondary opacity-60',
                                                $isSelected => 'bg-primary-subtle border-primary',
                                                default => 'bg-white',
                                            };
                                        @endphp

                                        <label wire:key="pq-{{ $pq->id }}"
                                            class="d-flex align-items-start gap-3 p-3 rounded-3 mb-2 border {{ $rowClass }}"
                                            style="{{ $alreadyInGroup ? 'cursor:not-allowed;' : 'cursor:pointer;' }}">

                                            <input type="checkbox" class="form-check-input mt-1 flex-shrink-0"
                                                @if (!$alreadyInGroup) wire:click="togglePickerQuestion({{ $pq->id }})" @endif
                                                {{ $isSelected ? 'checked' : '' }}
                                                {{ $alreadyInGroup ? 'disabled' : '' }}>

                                            <div class="flex-grow-1">
                                                <p
                                                    class="mb-1 small fw-medium {{ $alreadyInGroup ? 'text-muted' : 'text-dark' }}">
                                                    @php
                                                        $stemPreview = Str::limit(
                                                            html_entity_decode(
                                                                strip_tags($stemEn),
                                                                ENT_QUOTES | ENT_HTML5,
                                                                'UTF-8',
                                                            ),
                                                            120,
                                                        );
                                                    @endphp
                                                    {{ $stemPreview }}
                                                </p>

                                                <div class="d-flex flex-wrap gap-2 align-items-center">

                                                    {{-- Answer Category --}}
                                                    @if ($pq->answer_category === 'single_choice')
                                                        <span class="badge bg-primary-subtle text-primary"
                                                            style="font-size:.7rem;">Single Choice</span>
                                                    @elseif ($pq->answer_category === 'multi_choice')
                                                        <span class="badge bg-info-subtle text-info"
                                                            style="font-size:.7rem;">Multiple Choice</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary"
                                                            style="font-size:.7rem;">Open Ended</span>
                                                    @endif

                                                    {{-- Marks --}}
                                                    <span class="badge bg-success-subtle text-success"
                                                        style="font-size:.7rem;">
                                                        {{ $pq->question_content['marks'] }} Mark(s)
                                                    </span>

                                                    <code
                                                        style="font-size:.7rem; color:#6c757d;">{{ $pq->question_code }}</code>

                                                    <span class="badge bg-danger-subtle text-danger"
                                                        style="font-size:.7rem;">
                                                        {{ $pq->primarySkill->name }}
                                                    </span>
                                                    <span class="badge bg-warning-subtle text-warning"
                                                        style="font-size:.7rem;">
                                                        {{ $pq->subSkill->name }}
                                                    </span>
                                                    <span class="badge bg-secondary-subtle text-dark"
                                                        style="font-size:.7rem;">
                                                        Age-Group-{{ $pq->ageGroup->name }}
                                                    </span>
                                                    <span class="badge bg-info-subtle text-primary"
                                                        style="font-size:.7rem;">
                                                        Level-{{ $pq->difficultyLevel->level }}
                                                    </span>

                                                    {{-- Already in this section --}}
                                                    @if ($alreadyInGroup)
                                                        <span class="badge bg-secondary text-white"
                                                            style="font-size:.7rem;">
                                                            <i class="ri ri-check-double-line me-1"></i>Already in this
                                                            section
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>

                                            {{-- Preview Button --}}
                                            <div class="ms-auto">
                                                <button type="button"
                                                    wire:click.stop="viewQuestion({{ $pq->id }})"
                                                    class="btn btn-sm btn-outline-info" title="View Full Question">
                                                    <i class="ri ri-eye-line"></i>
                                                </button>
                                            </div>

                                        </label>
                                    @endforeach
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="ri ri-question-line fs-3 opacity-30"></i>
                                        <p class="small mt-2 mb-0">No questions available (all already added)</p>
                                    </div>
                                @endif

                            </div>

                            {{-- Pagination --}}
                            @if ($pickerQuestions && $pickerQuestions->hasPages())
                                <div class="px-3 pb-3">
                                    {{ $pickerQuestions->links('pagination::bootstrap-5') }}
                                </div>
                            @endif

                        @endif

                    </div>

                    @if ($pickerGroupId && count($pickerSelectedQIds) > 0)
                        <div class="card-footer bg-white py-2">
                            <button type="button" wire:click="addGroupToAssessment" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">
                                <span wire:loading wire:target="addGroupToAssessment">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Adding…
                                </span>
                                <span wire:loading.remove wire:target="addGroupToAssessment">
                                    <i class="ri ri-add-large-line me-1"></i>
                                    {{ $pickerMode === 'existing' ? 'Add Selected Questions' : 'Add to Assessment' }}
                                    ({{ count($pickerSelectedQIds) }})
                                </span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    @endif
    {{-- /group-picker view --}}


    {{-- ══════════════════════════════════════════════════════════════
         QUESTION PREVIEW MODAL (Bootstrap)
    ══════════════════════════════════════════════════════════════ --}}
    @if ($showQuestionPreview && !empty($previewQuestion))


        <div class="modal fade show d-block" tabindex="-1" style="z-index:9060;">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">

                    {{-- Modal Header --}}
                    <div class="modal-header border-bottom py-3"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                        <h5 class="modal-title text-white">
                            <i class="ri ri-eye-line me-2"></i>Question Preview
                        </h5>
                        <button type="button" wire:click="closeQuestionPreview"
                            class="btn-close btn-close-white"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body p-4">

                        {{-- Code & Marks --}}
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <code class="fs-6 text-primary">{{ $previewQuestion['code'] }}</code>
                                {{-- Answer Category Badge --}}
                                @if ($previewQuestion['answer_category'] === 'single_optional')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        Single Choice
                                    </span>
                                @elseif ($previewQuestion['answer_category'] === 'multi_optional')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        Multiple Choice
                                    </span>
                                @else
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        Open Ended
                                    </span>
                                @endif
                            </div>
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="ri ri-trophy-line me-1"></i>{{ $previewQuestion['marks'] }} Mark(s)
                            </span>
                        </div>

                        {{-- Passage (if exists) --}}


                        {{-- Question Stems (All Languages) --}}
                        @if (!empty($previewQuestion['stems']))
                            <div class="mb-4">
                                <h6 class="fw-semibold text-primary mb-3">
                                    <i class="ri ri-question-line me-1"></i>Question
                                </h6>

                                @foreach ($previewQuestion['stems'] as $lang => $stem)
                                    <div class="mb-3 p-3 bg-light rounded border">
                                        <span class="badge bg-primary mb-2">
                                            {{ strtoupper($lang) }}
                                        </span>
                                        <div class="small fw-medium text-dark">
                                            {!! $stem !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning mb-4">
                                <i class="ri ri-error-warning-line me-1"></i>
                                No question stem found.
                            </div>
                        @endif

                        {{-- Options --}}
                        @if (!empty($previewQuestion['options']))
                            <div class="mb-2">
                                <h6 class="fw-semibold text-success mb-3">
                                    <i class="ri ri-list-check me-1"></i>Options
                                    <small class="text-muted fw-normal ms-1">
                                        (correct answer highlighted in green)
                                    </small>
                                </h6>

                                @foreach ($previewQuestion['options'] as $opt)
                                    <div
                                        class="mb-3 rounded border overflow-hidden
                                            {{ $opt['is_correct'] ? 'border-success' : 'border-light' }}">

                                        {{-- Option Header --}}
                                        <div
                                            class="px-3 py-2 d-flex justify-content-between align-items-center
                                                {{ $opt['is_correct'] ? 'bg-success text-white' : 'bg-light text-muted' }}">
                                            <span class="fw-semibold small">
                                                Option {{ $opt['index'] }}
                                            </span>
                                            @if ($opt['is_correct'])
                                                <span class="badge bg-white text-success">
                                                    <i class="ri ri-check-line me-1"></i>Correct Answer
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Option Texts (all languages) --}}
                                        <div class="p-3 {{ $opt['is_correct'] ? 'bg-success-subtle' : 'bg-white' }}">
                                            @forelse ($opt['texts'] as $lang => $text)
                                                <div class="d-flex align-items-start gap-2 mb-2">
                                                    <span class="badge bg-secondary flex-shrink-0"
                                                        style="font-size:.65rem;margin-top:2px;">
                                                        {{ strtoupper($lang) }}
                                                    </span>
                                                    <span class="small">{{ $text }}</span>
                                                </div>
                                            @empty
                                                <span class="text-muted small">No text available</span>
                                            @endforelse
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if (!empty($previewQuestion['admin_note']))
                            <div class="mt-6 border-t pt-4">
                                <h6 class="text-sm font-semibold text-gray-700 mb-2">
                                    Admin Note
                                </h6>

                                <div class="rounded-lg bg-yellow-50   p-3 text-sm text-gray-700 whitespace-pre-wrap">
                                    {{ $previewQuestion['admin_note'] }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer border-top">
                        <button type="button" wire:click="closeQuestionPreview" class="btn btn-secondary mt-4">
                            <i class="ri ri-close-line me-1"></i>Close
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-backdrop fade show" style="z-index:1055;"></div>

    @endif
    {{-- /question preview modal --}}

</div>
