@php
    use App\Helper\Globals;
    $languages = Globals::LANGUAGES;
@endphp

<div class="assessment-build-view-wrapper">

    {{-- ══════════════════════════════════════════════════════════════
         READ-ONLY LOCKED BANNER
    ══════════════════════════════════════════════════════════════ --}}
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 border-warning shadow-sm" role="alert">
        <i class="ri ri-lock-line fs-4 text-warning"></i>
        <div>
            <strong>View Only Mode</strong> —
            This assessment is locked because students have already attempted it.
            No changes can be made.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-eye-line text-warning me-2"></i>Assessment Builder
                <span class="badge bg-warning text-dark ms-2" style="font-size:.75rem;">
                    <i class="ri ri-lock-line me-1"></i>Read Only
                </span>
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
                    <li class="breadcrumb-item active">Builder (View Only)</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.assessment-masters.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ri ri-arrow-left-line me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- ══════════════════════════════════════════════════════════
             LEFT — Groups + Questions (Read Only)
        ══════════════════════════════════════════════════════════ --}}
        <div class="col-lg-8">

            @if (empty($assessmentGroups))
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="ri ri-layout-grid-line" style="font-size:3rem;opacity:.3;"></i>
                        <h6 class="mt-3 fw-semibold">No Sections Found</h6>
                        <p class="small mb-0">This assessment has no sections configured.</p>
                    </div>
                </div>
            @else
                @foreach ($assessmentGroups as $agIndex => $ag)
                    <div class="card shadow-sm border-0 mb-4"
                        wire:key="agv-{{ $agIndex }}-{{ $ag['question_group_id'] }}">

                        {{-- Group Header --}}
                        <div class="card-header py-3 border-bottom"
                            style="background:linear-gradient(135deg,#D9D979,#FFFFEE);">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="badge bg-primary fw-semibold">Group {{ $loop->iteration }}</span>
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
                                        {{-- Lock indicator --}}
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="ri ri-lock-line me-1"></i>Locked
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <code>{{ $ag['group_code'] }}</code> •
                                        {{ count($ag['questions']) }} question(s)
                                    </small>
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

                                {{-- Group Settings (Read Only) --}}
                                <div class="p-4 border-bottom bg-light">
                                    <div class="row g-3">

                                        @if ($ag['instructions'])
                                            <div class="col-12">
                                                <label class="form-label fw-medium small text-muted">Group
                                                    Instructions</label>
                                                <div class="form-control form-control-sm bg-white text-dark"
                                                    style="min-height:60px;cursor:default;">
                                                    {{ $ag['instructions'] ?: '—' }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small text-muted">
                                                <i class="ri ri-timer-line me-1"></i>Group Timer (sec)
                                            </label>
                                            <div class="form-control form-control-sm bg-white text-dark"
                                                style="cursor:default;">
                                                {{ $ag['group_timer'] ?: '0 (no timer)' }}
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label
                                                class="form-label fw-medium small text-muted d-block">Settings</label>
                                            <div class="d-flex flex-wrap gap-4 mt-1">

                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge {{ $ag['suffle_question'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                        {{ $ag['suffle_question'] ? 'Shuffle On' : 'Shuffle Off' }}
                                                    </span>
                                                    <span class="small text-muted">Shuffle Questions</span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge {{ $ag['allow_back_to_group_question'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                        {{ $ag['allow_back_to_group_question'] ? 'Allowed' : 'Disabled' }}
                                                    </span>
                                                    <span class="small text-muted">Back to Group Question</span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge {{ $ag['allow_back_to_previous_question'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                        {{ $ag['allow_back_to_previous_question'] ? 'Allowed' : 'Disabled' }}
                                                    </span>
                                                    <span class="small text-muted">Back (Question)</span>
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
                                        <p class="small mt-2 mb-0">No questions in this group.</p>
                                    </div>
                                @else
                                    @foreach ($ag['questions'] as $qIndex => $question)
                                        <div class="border rounded-3 mb-3 overflow-hidden"
                                            wire:key="aqv-{{ $agIndex }}-{{ $qIndex }}-{{ $question['question_id'] }}">

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

                                                {{-- Preview Button Only --}}
                                                <button type="button"
                                                    wire:click="viewQuestion({{ $question['question_id'] }})"
                                                    class="btn btn-sm btn-outline-info flex-shrink-0"
                                                    data-bs-toggle="tooltip" title="Preview Question">
                                                    <i class="ri ri-eye-line"></i>
                                                </button>
                                            </div>

                                            {{-- Question Settings (Read Only) --}}
                                            <div class="p-3 bg-white">
                                                <div class="row g-2 align-items-center">

                                                    <div class="col-md-3">
                                                        <label class="form-label fw-medium mb-1 text-muted"
                                                            style="font-size:.75rem;">
                                                            <i class="ri ri-timer-line me-1"></i>Timer (sec)
                                                        </label>
                                                        <div class="form-control form-control-sm bg-light text-dark"
                                                            style="cursor:default;">
                                                            {{ $question['question_timer'] ?: '0' }}
                                                        </div>
                                                    </div>

                                                    @if ($has_negative_mark)
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-medium mb-1 text-muted"
                                                                style="font-size:.75rem;">
                                                                <i
                                                                    class="ri ri-subtract-line me-1 text-danger"></i>Negative
                                                                Mark
                                                            </label>
                                                            <div class="form-control form-control-sm bg-light text-danger border-danger"
                                                                style="cursor:default;">
                                                                {{ $question['negative_mark'] ?: '0' }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>

                        {{-- No Footer Actions in View Mode --}}
                        <div class="card-footer bg-light py-2 text-muted small">
                            <i class="ri ri-lock-line me-1"></i>
                            This section is locked and cannot be modified.
                        </div>

                    </div>
                @endforeach
            @endif

        </div>
        {{-- /left --}}

        {{-- ══════════════════════════════════════════════════════════
             RIGHT — Summary (Read Only)
        ══════════════════════════════════════════════════════════ --}}
        <div class="col-lg-4">

            {{-- Assessment Info Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="ri ri-draft-line text-primary me-2"></i>Assessment Info
                    </h6>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                        <i class="ri ri-lock-line me-1"></i>Locked
                    </span>
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
                            <span class="badge bg-warning-subtle text-warning">{{ $ageGroup }}</span>
                        </li>

                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Difficulty Level</span>
                            <span class="badge bg-danger-subtle text-danger">{{ $difficultLevel }}</span>
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
            </div>

        </div>
        {{-- /right --}}

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         QUESTION PREVIEW MODAL (same as builder)
    ══════════════════════════════════════════════════════════════ --}}
    @if ($showQuestionPreview && !empty($previewQuestion))
        <div class="modal fade show d-block" tabindex="-1" style="z-index:9060;">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header border-bottom py-3"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                        <h5 class="modal-title text-white">
                            <i class="ri ri-eye-line me-2"></i>Question Preview
                        </h5>
                        <button type="button" wire:click="closeQuestionPreview"
                            class="btn-close btn-close-white"></button>
                    </div>

                    <div class="modal-body p-4">

                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <code class="fs-6 text-primary">{{ $previewQuestion['code'] }}</code>
                                @if ($previewQuestion['answer_category'] === 'single_optional')
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle">Single
                                        Choice</span>
                                @elseif ($previewQuestion['answer_category'] === 'multi_optional')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">Multiple
                                        Choice</span>
                                @else
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Open
                                        Ended</span>
                                @endif
                            </div>
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="ri ri-trophy-line me-1"></i>{{ $previewQuestion['marks'] }} Mark(s)
                            </span>
                        </div>

                        @if (!empty($previewQuestion['stems']))
                            <div class="mb-4">
                                <h6 class="fw-semibold text-primary mb-3">
                                    <i class="ri ri-question-line me-1"></i>Question
                                </h6>
                                @foreach ($previewQuestion['stems'] as $lang => $stem)
                                    <div class="mb-3 p-3 bg-light rounded border">
                                        <span class="badge bg-primary mb-2">{{ strtoupper($lang) }}</span>
                                        <div class="small fw-medium text-dark">{!! $stem !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning mb-4">
                                <i class="ri ri-error-warning-line me-1"></i>No question stem found.
                            </div>
                        @endif

                        @if (!empty($previewQuestion['options']))
                            <div class="mb-2">
                                <h6 class="fw-semibold text-success mb-3">
                                    <i class="ri ri-list-check me-1"></i>Options
                                    <small class="text-muted fw-normal ms-1">(correct answer highlighted in
                                        green)</small>
                                </h6>
                                @foreach ($previewQuestion['options'] as $opt)
                                    <div
                                        class="mb-3 rounded border overflow-hidden
                                            {{ $opt['is_correct'] ? 'border-success' : 'border-light' }}">
                                        <div
                                            class="px-3 py-2 d-flex justify-content-between align-items-center
                                                {{ $opt['is_correct'] ? 'bg-success text-white' : 'bg-light text-muted' }}">
                                            <span class="fw-semibold small">Option {{ $opt['index'] }}</span>
                                            @if ($opt['is_correct'])
                                                <span class="badge bg-white text-success">
                                                    <i class="ri ri-check-line me-1"></i>Correct Answer
                                                </span>
                                            @endif
                                        </div>
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
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="small fw-semibold text-muted mb-2">Admin Note</h6>
                                <div class="rounded bg-warning-subtle p-3 small text-dark"
                                    style="white-space:pre-wrap;">
                                    {{ $previewQuestion['admin_note'] }}
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" wire:click="closeQuestionPreview" class="btn btn-secondary">
                            <i class="ri ri-close-line me-1"></i>Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" style="z-index:1055;"></div>
    @endif

</div>
