@php
    use App\Helper\Globals;
    $languages = Globals::LANGUAGES;
@endphp

<div class="question-group-wrapper"
     x-data="{ activeTab: '{{ array_key_first($languages) }}' }">

    {{-- ══════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════════════════════════ --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show
                    d-flex align-items-center mb-4" role="alert">
            <i class="ri ri-checkbox-circle-line me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show
                    d-flex align-items-center mb-4" role="alert">
            <i class="ri ri-error-warning-line me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="ri ri-error-warning-fill me-2 fs-5"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 mt-1 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════
         ██████  VIEW 1 — GROUP FORM  (Create / Edit Group Info)
    ══════════════════════════════════════════════════════════════ --}}
  {{-- ══════════════════════════════════════════════════════════════
     ██████  VIEW 1 — GROUP FORM
══════════════════════════════════════════════════════════════ --}}
@if ($view === 'group_form')

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-folder-2-fill text-primary me-2"></i>
                {{ $groupId ? 'Edit Question Group' : 'Create Question Group' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.question-groups') }}">
                            Question Groups
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $groupId ? 'Edit Group' : 'New Group' }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            @if ($groupId)
                <button type="button"
                        wire:click="cancelGroupEdit"
                        class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Cancel
                </button>
            @else
                <a href="{{ route('admin.question-groups') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Back
                </a>
            @endif

            <button type="button"
                    wire:click="saveGroup"
                    wire:loading.attr="disabled"
                    class="btn btn-primary btn-sm shadow-sm">
                <span wire:loading wire:target="saveGroup">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Saving…
                </span>
                <span wire:loading.remove wire:target="saveGroup">
                    <i class="ri ri-save-line me-1"></i>
                    {{ $groupId ? 'Update Group' : 'Save & Continue' }}
                </span>
            </button>
        </div>
    </div>

    <form wire:submit.prevent="saveGroup">
        <div class="row g-4">

            {{-- ── LEFT COLUMN ─────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- ┌──────────────────────────────────────────────┐
                     │  GROUP BASIC INFO                            │
                     └──────────────────────────────────────────────┘ --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-information-line text-primary me-2"></i>
                            Group Information
                        </h6>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Group Code --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Group Code
                                </label>
                                <div class="form-control bg-light text-muted">
                                    {{ $group_code }}
                                </div>
                                <input type="hidden" wire:model="group_code">
                            </div>

                            {{-- Questions Category --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Questions Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select wire:model.live="questions_category"
                                        class="form-select @error('questions_category') is-invalid @enderror">
                                    <option value="single">Single</option>
                                    <option value="multiple">Multiple</option>
                                </select>
                                @error('questions_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Helper text --}}
                                <div class="mt-1">
                                    @if ($questions_category === 'multiple')
                                        <small class="text-info">
                                            <i class="ri ri-information-line me-1"></i>
                                            Multiple: A passage / context is shown above all questions.
                                        </small>
                                    @else
                                        <small class="text-muted">
                                            <i class="ri ri-information-line me-1"></i>
                                            Single: Each question is standalone.
                                        </small>
                                    @endif
                                </div>
                            </div>

                            {{-- Admin Note --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">
                                    Admin Note
                                </label>
                                <input type="text"
                                       wire:model="admin_note"
                                       class="form-control"
                                       placeholder="Internal note...">
                            </div>

                        </div>
                    </div>
                </div>


                {{-- ┌──────────────────────────────────────────────┐
                     │  GROUP TITLE  (Multi Language)               │
                     └──────────────────────────────────────────────┘ --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3
                                d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-translate-2 text-primary me-2"></i>
                            Group Title
                        </h6>

                        {{-- Language Tabs --}}
                        <ul class="nav nav-pills nav-sm mb-0">
                            @foreach ($languages as $langCode => $lang)
                                <li class="nav-item">
                                    <button type="button"
                                            @click="activeTab = '{{ $langCode }}'"
                                            class="nav-link py-1 px-3"
                                            :class="{ 'active': activeTab === '{{ $langCode }}' }">
                                        {{ $lang['flag'] }} {{ $lang['label'] }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        @foreach ($languages as $langCode => $lang)
                            <div x-show="activeTab === '{{ $langCode }}'" x-cloak>
                                <label class="form-label fw-medium small mb-2">
                                    {{ $lang['flag'] }} {{ $lang['label'] }} — Group Title
                                </label>
                                <textarea
                                    wire:model="group_content.title.{{ $langCode }}"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Enter group title in {{ $lang['label'] }}...">
                                </textarea>
                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- ┌──────────────────────────────────────────────┐
                     │  GROUP CONTENT — Quill Multilanguage         │
                     │  Only shown when category = 'multiple'       │
                     └──────────────────────────────────────────────┘ --}}
                @if ($questions_category === 'multiple')

                    <div class="card shadow-sm border-0 mb-4"
                         x-data="{ contentTab: '{{ array_key_first($languages) }}' }">

                        <div class="card-header bg-white border-bottom py-3
                                    d-flex align-items-center justify-content-between">

                            <div class="d-flex align-items-center gap-2">
                                <i class="ri ri-file-text-line text-primary fs-5"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        Group Content / Passage
                                    </h6>
                                    <small class="text-muted">
                                        This text is shown above all questions in this group.
                                    </small>
                                </div>
                                <span class="badge bg-info-subtle text-info
                                             border border-info-subtle ms-1">
                                    Multiple Category
                                </span>
                            </div>

                            {{-- Content Language Tabs (independent from title tabs) --}}
                            <ul class="nav nav-pills nav-sm mb-0">
                                @foreach ($languages as $langCode => $lang)
                                    <li class="nav-item">
                                        <button type="button"
                                                @click="contentTab = '{{ $langCode }}'"
                                                class="nav-link py-1 px-3"
                                                :class="{ 'active': contentTab === '{{ $langCode }}' }">
                                            {{ $lang['flag'] }} {{ $lang['label'] }}
                                            @if ($langCode === 'en')
                                                <span class="text-danger ms-1">*</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body p-4">

                            @error('group_content.content.en')
                                <div class="alert alert-danger py-2 small mb-3">
                                    <i class="ri ri-error-warning-line me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            @foreach ($languages as $langCode => $lang)

                                <div x-show="contentTab === '{{ $langCode }}'" x-cloak>

                                    <label class="form-label fw-medium small mb-2">
                                        {{ $lang['flag'] }} {{ $lang['label'] }} — Group Content
                                        @if ($langCode === 'en')
                                            <span class="text-danger">*</span>
                                        @endif
                                        <span class="text-muted fw-normal ms-1">
                                            (Passage / context / instructions)
                                        </span>
                                    </label>

                                    {{-- Quill Editor — wire:ignore prevents Livewire clobbering the DOM --}}
                                    <div
                                        wire:ignore
                                        x-data="{
                                            content: @js($group_content['content'][$langCode] ?? '')
                                        }"
                                        x-init="
                                            const groupQuill_{{ $langCode }} = new Quill(
                                                $refs.groupContentEditor_{{ $langCode }},
                                                {
                                                    theme: 'snow',
                                                    placeholder: 'Enter passage / context in {{ $lang['label'] }}...',
                                                    modules: {
                                                        toolbar: fullToolbar,
                                                        syntax: true,
                                                        formula: true,
                                                        'table-better': {
                                                            language: 'en_US',
                                                            menus: [
                                                                'column','row','merge',
                                                                'table','cell','wrap',
                                                                'copy','delete'
                                                            ],
                                                            toolbarTable: true
                                                        },
                                                        keyboard: {
                                                            bindings: QuillTableBetter.keyboardBindings
                                                        }
                                                    }
                                                }
                                            );

                                            {{-- Pre-fill editor with existing content --}}
                                            if (content) {
                                                groupQuill_{{ $langCode }}.root.innerHTML = content;
                                            }

                                            {{-- Sync to Livewire on blur / focus-out --}}
                                            groupQuill_{{ $langCode }}.on('selection-change', function(range) {
                                                if (range === null) {
                                                    $wire.set(
                                                        'group_content.content.{{ $langCode }}',
                                                        groupQuill_{{ $langCode }}.root.innerHTML
                                                    );
                                                }
                                            });

                                            {{-- Also sync on text-change for real-time safety --}}
                                            groupQuill_{{ $langCode }}.on('text-change', function() {
                                                $wire.set(
                                                    'group_content.content.{{ $langCode }}',
                                                    groupQuill_{{ $langCode }}.root.innerHTML
                                                );
                                            });
                                        "
                                    >
                                        <div x-ref="groupContentEditor_{{ $langCode }}"
                                             style="min-height: 280px;"></div>
                                    </div>

                                </div>

                            @endforeach

                        </div>

                        {{-- Word count / helper footer --}}
                        <div class="card-footer bg-light border-top py-2 px-4">
                            <small class="text-muted">
                                <i class="ri ri-information-line me-1"></i>
                                Supports rich text, tables, formulas and images.
                                English content is required; other languages are optional.
                            </small>
                        </div>
                    </div>

                @endif
                {{-- /group_content quill --}}

            </div>
            {{-- /left --}}


            {{-- ── RIGHT COLUMN ────────────────────────────────── --}}
            <div class="col-lg-4">

                {{-- How it works --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-information-2-line text-primary me-2"></i>
                            How it works
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0 small">

                            <li class="mb-3 d-flex gap-2">
                                <span class="badge bg-primary rounded-pill
                                             flex-shrink-0 mt-1"
                                      style="width:22px;height:22px;
                                             display:inline-flex !important;
                                             align-items:center;
                                             justify-content:center;">
                                    1
                                </span>
                                <span class="text-muted">
                                    Fill in group details and save.
                                </span>
                            </li>

                            <li class="mb-3 d-flex gap-2">
                                <span class="badge bg-primary rounded-pill
                                             flex-shrink-0 mt-1"
                                      style="width:22px;height:22px;
                                             display:inline-flex !important;
                                             align-items:center;
                                             justify-content:center;">
                                    2
                                </span>
                                <span class="text-muted">
                                    Add questions one by one.
                                </span>
                            </li>

                            <li class="mb-3 d-flex gap-2">
                                <span class="badge bg-primary rounded-pill
                                             flex-shrink-0 mt-1"
                                      style="width:22px;height:22px;
                                             display:inline-flex !important;
                                             align-items:center;
                                             justify-content:center;">
                                    3
                                </span>
                                <span class="text-muted">
                                    Edit group or questions anytime.
                                </span>
                            </li>

                            {{-- Extra tip for multiple --}}
                            @if ($questions_category === 'multiple')
                                <li class="mt-3 pt-3 border-top d-flex gap-2">
                                    <i class="ri ri-lightbulb-line text-warning flex-shrink-0 mt-1"></i>
                                    <span class="text-muted">
                                        <strong>Multiple category:</strong>
                                        The passage you write will be shown
                                        above all questions in this group
                                        during the assessment.
                                    </span>
                                </li>
                            @endif

                        </ul>
                    </div>

                    <div class="card-footer bg-white p-3">
                        <button type="button"
                                wire:click="saveGroup"
                                wire:loading.attr="disabled"
                                class="btn btn-primary w-100">
                            <span wire:loading wire:target="saveGroup">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Saving…
                            </span>
                            <span wire:loading.remove wire:target="saveGroup">
                                <i class="ri ri-save-line me-1"></i>
                                {{ $groupId ? 'Update Group' : 'Save & Continue' }}
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Category Info Card --}}
                <div class="card border-0 mb-4
                    {{ $questions_category === 'multiple'
                        ? 'bg-info-subtle border border-info-subtle'
                        : 'bg-light' }}">
                    <div class="card-body p-3">

                        @if ($questions_category === 'multiple')
                            <div class="d-flex gap-2">
                                <i class="ri ri-file-copy-2-line text-info fs-5 flex-shrink-0"></i>
                                <div>
                                    <p class="fw-semibold text-info mb-1 small">
                                        Multiple Category Selected
                                    </p>
                                    <p class="text-muted small mb-0">
                                        A rich text passage editor is available
                                        in 4 languages. Students will read this
                                        passage before answering questions.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="d-flex gap-2">
                                <i class="ri ri-question-line text-muted fs-5 flex-shrink-0"></i>
                                <div>
                                    <p class="fw-semibold text-muted mb-1 small">
                                        Single Category Selected
                                    </p>
                                    <p class="text-muted small mb-0">
                                        Each question is standalone.
                                        No passage/context is needed.
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
            {{-- /right --}}

        </div>
    </form>

@endif
{{-- /group_form --}}


    {{-- ══════════════════════════════════════════════════════════════
         ██████  VIEW 2 — GROUP VIEW  (Readonly Group + Questions List)
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'group_view')

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-folder-2-fill text-primary me-2"></i>
                    {{ $group_content['title'][array_key_first($languages)] ?? $group_code }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.question-groups') }}">
                                Question Groups
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Group Detail</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.question-groups') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Back to List
                </a>

                @if (! $isGroupLocked)
                    <button type="button"
                            wire:click="editGroup"
                            class="btn btn-outline-primary btn-sm">
                        <i class="ri ri-pencil-line me-1"></i> Edit Group
                    </button>
                @endif

                <button type="button"
                        wire:click="addNewQuestion"
                        class="btn btn-primary btn-sm shadow-sm">
                    <i class="ri ri-add-large-line me-1"></i> Add Question
                </button>
            </div>
        </div>

        <div class="row g-4">

            {{-- Left — Questions List --}}
            <div class="col-lg-8">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3
                                d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-questionnaire-line text-primary me-2"></i>
                            Questions
                            <span class="badge bg-primary ms-1">
                                {{ count($questionsList) }}
                            </span>
                        </h6>
                        <button type="button"
                                wire:click="addNewQuestion"
                                class="btn btn-sm btn-outline-primary">
                            <i class="ri ri-add-large-line me-1"></i> Add Question
                        </button>
                    </div>

                    <div class="card-body p-0">

                        @forelse ($questionsList as $qi => $q)

                            <div class="border-bottom px-4 py-3
                                        hover-bg-light transition
                                        {{ $q['in_assessment'] ? 'bg-warning-subtle' : '' }}"
                                 wire:key="qlist-{{ $q['id'] }}">

                                <div class="d-flex align-items-start justify-content-between gap-3">

                                    {{-- Left info --}}
                                    <div class="d-flex align-items-start gap-3">

                                        {{-- Number --}}
                                        <span class="badge bg-primary-subtle text-primary
                                                     fw-bold rounded-circle d-inline-flex
                                                     align-items-center justify-content-center
                                                     flex-shrink-0 mt-1"
                                              style="width:32px;height:32px;">
                                            {{ $qi + 1 }}
                                        </span>

                                        <div>
                                            {{-- Stem preview --}}
                                            <p class="mb-1 fw-medium text-dark small">
                                                {{ Str::limit($q['stem_en'] ?: '(No English stem)', 80) }}
                                            </p>

                                            <div class="d-flex flex-wrap gap-2">

                                                {{-- Answer category --}}
                                                @if ($q['answer_category'] === 'single_optional')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                                        <i class="ri ri-record-circle-line me-1"></i>Single
                                                    </span>
                                                @elseif ($q['answer_category'] === 'multi_optional')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                                        <i class="ri ri-checkbox-multiple-line me-1"></i>Multi
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">
                                                        <i class="ri ri-text me-1"></i>Open Text
                                                    </span>
                                                @endif

                                                {{-- Marks --}}
                                                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                                    <i class="ri ri-trophy-line me-1"></i>
                                                    {{ $q['marks'] }} Mark(s)
                                                </span>

                                                {{-- Options count --}}
                                                @if ($q['answer_category'] !== 'open_text')
                                                    <span class="badge bg-light text-dark border small">
                                                        <i class="ri ri-list-check-3 me-1"></i>
                                                        {{ $q['options_count'] }} Options
                                                    </span>
                                                @endif

                                                {{-- Difficulty --}}
                                                <span class="badge bg-light text-dark border small">
                                                    {{ $q['difficulty'] }}
                                                </span>

                                                {{-- In assessment --}}
                                                @if ($q['in_assessment'])
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle small">
                                                        <i class="ri ri-lock-line me-1"></i>In Assessment
                                                    </span>
                                                @endif

                                            </div>

                                            <p class="mb-0 mt-1 text-muted"
                                               style="font-size:.75rem;">
                                                {{ $q['question_code'] }} •
                                                {{ $q['primary_skill'] }} •
                                                {{ $q['age_group'] }}
                                            </p>
                                        </div>

                                    </div>

                                    {{-- Actions --}}
                                    <div class="d-flex gap-2 flex-shrink-0">

                                        @if (! $q['in_assessment'])
                                            <button type="button"
                                                    wire:click="editQuestion({{ $q['id'] }})"
                                                    class="btn btn-sm btn-outline-primary">
                                                <i class="ri ri-pencil-line"></i>
                                            </button>

                                            <button type="button"
                                                    wire:click="deleteQuestion({{ $q['id'] }})"
                                                    wire:confirm="Delete this question permanently?"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    wire:click="editQuestion({{ $q['id'] }})"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    disabled
                                                    title="Used in assessment">
                                                <i class="ri ri-eye-line"></i>
                                            </button>
                                        @endif

                                    </div>
                                </div>

                            </div>

                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="ri ri-questionnaire-line"
                                   style="font-size:3rem;opacity:.3;"></i>
                                <h6 class="mt-3 fw-semibold">No Questions Yet</h6>
                                <p class="small mb-4">
                                    Click "Add Question" to start building this group.
                                </p>
                                <button type="button"
                                        wire:click="addNewQuestion"
                                        class="btn btn-primary btn-sm">
                                    <i class="ri ri-add-large-line me-1"></i>
                                    Add First Question
                                </button>
                            </div>
                        @endforelse

                    </div>
                </div>

            </div>

            {{-- Right — Group Info Card --}}
            <div class="col-lg-4">

                {{-- Group Info --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3
                                d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-folder-info-line text-primary me-2"></i>
                            Group Info
                        </h6>
                        @if (! $isGroupLocked)
                            <button type="button"
                                    wire:click="editGroup"
                                    class="btn btn-sm btn-outline-primary">
                                <i class="ri ri-pencil-line me-1"></i>Edit
                            </button>
                        @endif
                    </div>

                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Code</span>
                                <code class="small">{{ $group_code }}</code>
                            </li>

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Category</span>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ ucfirst($questions_category) }}
                                </span>
                            </li>

                            @if ($isGroupLocked)
                                <li class="list-group-item px-0 d-flex
                                           justify-content-between align-items-center">
                                    <span class="small text-muted">Status</span>
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="ri ri-lock-line me-1"></i>
                                        In Assessment
                                    </span>
                                </li>
                            @endif

                            @if ($admin_note)
                                <li class="list-group-item px-0">
                                    <span class="small text-muted d-block mb-1">
                                        Admin Note
                                    </span>
                                    <span class="small">{{ $admin_note }}</span>
                                </li>
                            @endif

                            {{-- Titles per language --}}
                            @foreach ($languages as $langCode => $lang)
                                @if (!empty($group_content['title'][$langCode]))
                                    <li class="list-group-item px-0">
                                        <span class="small text-muted d-block mb-1">
                                            {{ $lang['flag'] }} {{ $lang['label'] }} Title
                                        </span>
                                        <span class="small fw-medium">
                                            {{ $group_content['title'][$langCode] }}
                                        </span>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="ri ri-bar-chart-box-line text-info me-2"></i>
                            Summary
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Total Questions</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ count($questionsList) }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Single Optional</span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">
                                    {{ collect($questionsList)->where('answer_category','single_optional')->count() }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Multi Optional</span>
                                <span class="badge bg-info-subtle text-info rounded-pill">
                                    {{ collect($questionsList)->where('answer_category','multi_optional')->count() }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Open Text</span>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ collect($questionsList)->where('answer_category','open_text')->count() }}
                                </span>
                            </li>

                            <li class="list-group-item px-0 d-flex
                                       justify-content-between align-items-center">
                                <span class="small text-muted">Total Marks</span>
                                <span class="badge bg-success rounded-pill">
                                    {{ collect($questionsList)->sum('marks') }}
                                </span>
                            </li>

                        </ul>
                    </div>
                </div>

            </div>

        </div>

    @endif
    {{-- /group_view --}}


    {{-- ══════════════════════════════════════════════════════════════
         ██████  VIEW 3 — QUESTION FORM  (Add / Edit single question)
    ══════════════════════════════════════════════════════════════ --}}
    @if ($view === 'question_form' && !empty($activeQuestion))

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="ri ri-questionnaire-fill text-primary me-2"></i>
                    {{ $activeQuestion['id'] ? 'Edit Question' : 'Add Question' }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.question-groups') }}">
                                Question Groups
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" wire:click.prevent="cancelQuestion">
                                {{ $group_content['title'][array_key_first($languages)] ?? $group_code }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $activeQuestion['id'] ? 'Edit Question' : 'New Question' }}
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <button type="button"
                        wire:click="cancelQuestion"
                        class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Back to Group
                </button>

                <button type="button"
                        wire:click="saveQuestion"
                        wire:loading.attr="disabled"
                        class="btn btn-primary btn-sm shadow-sm">
                    <span wire:loading wire:target="saveQuestion">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving…
                    </span>
                    <span wire:loading.remove wire:target="saveQuestion">
                        <i class="ri ri-save-line me-1"></i>
                        Save Question
                    </span>
                </button>
            </div>
        </div>

        <form wire:submit.prevent="saveQuestion">
            <div class="row g-4">

                {{-- ── Left Column ─────────────────────────────── --}}
                <div class="col-lg-8">

                    {{-- ── Basic Information ───────────────────── --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-information-line text-primary me-2"></i>
                                Basic Information
                            </h6>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3">

                                {{-- Question Code --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Question Code
                                    </label>
                                    <div class="form-control bg-light text-muted small">
                                        {{ $activeQuestion['question_code'] }}
                                    </div>
                                </div>

                                {{-- Answer Category --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Answer Category
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        wire:model.live="activeQuestion.answer_category"
                                        class="form-select @error('activeQuestion.answer_category') is-invalid @enderror">
                                        <option value="single_optional">Single Optional</option>
                                        <option value="multi_optional">Multi Optional</option>
                                        <option value="open_text">Open Text</option>
                                    </select>
                                    @error('activeQuestion.answer_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Marks --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Marks
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           wire:model="activeQuestion.marks"
                                           class="form-control @error('activeQuestion.marks') is-invalid @enderror"
                                           min="0"
                                           step="0.5">
                                    @error('activeQuestion.marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Primary Skill --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Primary Skill
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        wire:model="activeQuestion.primary_skill_id"
                                        class="form-select @error('activeQuestion.primary_skill_id') is-invalid @enderror">
                                        <option value="">— Select Skill —</option>
                                        @foreach ($primarySkillTypes as $skill)
                                            <option value="{{ $skill['id'] }}">
                                                {{ $skill['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('activeQuestion.primary_skill_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Sub Skill --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Sub Skill
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        wire:model="activeQuestion.sub_skill_id"
                                        class="form-select @error('activeQuestion.sub_skill_id') is-invalid @enderror">
                                        <option value="">— Select Sub Skill —</option>
                                        @foreach ($subSkillTypes as $skill)
                                            <option value="{{ $skill['id'] }}">
                                                {{ $skill['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('activeQuestion.sub_skill_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Difficulty Level --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Difficulty Level
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        wire:model="activeQuestion.difficulty_level_id"
                                        class="form-select @error('activeQuestion.difficulty_level_id') is-invalid @enderror">
                                        <option value="">— Select Difficulty —</option>
                                        @foreach ($difficultyLevels as $level)
                                            <option value="{{ $level['id'] }}">
                                                {{ $level['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('activeQuestion.difficulty_level_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Age Group --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">
                                        Age Group
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        wire:model="activeQuestion.age_group_id"
                                        class="form-select @error('activeQuestion.age_group_id') is-invalid @enderror">
                                        <option value="">— Select Age Group —</option>
                                        @foreach ($ageGroups as $group)
                                            <option value="{{ $group['id'] }}">
                                                {{ $group['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('activeQuestion.age_group_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- ── Question Stem ────────────────────────── --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3
                                    d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-translate-2 text-primary me-2"></i>
                                Question Stem
                            </h6>
                            <ul class="nav nav-pills nav-sm mb-0">
                                @foreach ($languages as $langCode => $lang)
                                    <li class="nav-item">
                                        <button type="button"
                                                @click="activeTab = '{{ $langCode }}'"
                                                class="nav-link py-1 px-3"
                                                :class="{ 'active': activeTab === '{{ $langCode }}' }">
                                            {{ $lang['flag'] }} {{ $lang['label'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            @foreach ($languages as $langCode => $lang)
                                <div x-show="activeTab === '{{ $langCode }}'" x-cloak>
                                    <label class="form-label fw-medium small mb-2">
                                        {{ $lang['flag'] }} {{ $lang['label'] }} — Question Stem
                                        @if ($langCode === 'en')
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    <div wire:ignore
                                         x-data="{
                                             content: @js($activeQuestion['stem'][$langCode] ?? '')
                                         }"
                                         x-init="
                                             const quill = new Quill($refs.editor_{{ $langCode }}, {
                                                 theme: 'snow',
                                                 placeholder: 'Enter question stem in {{ $lang['label'] }}...',
                                                 modules: {
                                                        toolbar: fullToolbar,
                                                        syntax: true,
                                                        formula: true,
                                                        table: false,
                                                        'table-better': {
                                                            language: 'en_US',
                                                            menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'copy', 'delete'],
                                                            toolbarTable: true,
                                                        },
                                                        keyboard: {
                                                            bindings: QuillTableBetter.keyboardBindings
                                                        }
                                                    }
                                                });

                                             if (content) {
                                                 quill.root.innerHTML = content;
                                             }

                                             quill.on('selection-change', function(range) {
                                                 if (range === null) {
                                                     $wire.set(
                                                         'activeQuestion.stem.{{ $langCode }}',
                                                         quill.root.innerHTML
                                                     );
                                                 }
                                             });
                                         ">
                                        <div x-ref="editor_{{ $langCode }}"
                                             style="height:200px;"></div>
                                    </div>

                                    @error("activeQuestion.stem.{$langCode}")
                                        <div class="text-danger small mt-1">
                                            <i class="ri ri-error-warning-line me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>


                    {{-- ── Stem Image ───────────────────────────── --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3
                                    d-flex align-items-center gap-2">
                            <i class="ri ri-image-line text-primary"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Question Image</h6>
                            <span class="badge bg-secondary fw-normal ms-1">Optional</span>
                        </div>

                        <div class="card-body p-4">

                            {{-- Existing image --}}
                            @if (!empty($activeQuestion['existing_image']))
                                <div class="mb-3">
                                    <p class="small text-muted mb-2">Current image:</p>
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ Storage::url($activeQuestion['existing_image']) }}"
                                             alt="Question image"
                                             class="img-thumbnail rounded"
                                             style="max-height:180px;object-fit:contain;">
                                        <button type="button"
                                                wire:click="removeStemImagePath"
                                                class="btn btn-danger btn-sm position-absolute
                                                       top-0 end-0 m-1 rounded-circle p-0"
                                                style="width:24px;height:24px;line-height:1;">
                                            <i class="ri ri-close-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Dropzone --}}
                            @if (empty($activeQuestion['existing_image']))
                                <div x-data="{ dragging: false }"
                                     @dragover.prevent="dragging = true"
                                     @dragleave.prevent="dragging = false"
                                     @drop.prevent="
                                         dragging = false;
                                         $refs.stemFile.files = $event.dataTransfer.files;
                                         $refs.stemFile.dispatchEvent(new Event('change'))
                                     "
                                     :class="dragging
                                             ? 'border-primary bg-primary bg-opacity-5'
                                             : 'border-secondary'"
                                     class="upload-dropzone border border-2 border-dashed
                                            rounded-3 text-center p-4"
                                     style="cursor:pointer;"
                                     @click="$refs.stemFile.click()">

                                    <input type="file"
                                           x-ref="stemFile"
                                           wire:model="stemImageUpload"
                                           accept="image/jpeg,image/png,image/gif"
                                           class="d-none">

                                    <div wire:loading wire:target="stemImageUpload"
                                         class="text-muted small">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Uploading…
                                    </div>
                                    <div wire:loading.remove wire:target="stemImageUpload">
                                        <i class="ri ri-upload-cloud-2-line fs-2 text-muted"></i>
                                        <p class="mb-1 small fw-medium text-dark mt-1">
                                            Click or drag &amp; drop
                                        </p>
                                        <p class="mb-0 small text-muted">
                                            JPEG, PNG, GIF — max 2 MB
                                        </p>
                                    </div>
                                </div>

                                @error('stemImageUpload')
                                    <div class="text-danger small mt-1">
                                        <i class="ri ri-error-warning-line me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            @endif

                            {{-- Staged preview --}}
                            @if ($stemImageUpload)
                                <div class="mt-3 d-flex align-items-start gap-3">
                                    <img src="{{ $stemImageUpload->temporaryUrl() }}"
                                         alt="Preview"
                                         class="img-thumbnail rounded"
                                         style="max-height:140px;object-fit:contain;">
                                    <div>
                                        <p class="small fw-medium mb-1 text-dark">
                                            {{ $stemImageUpload->getClientOriginalName() }}
                                        </p>
                                        <p class="small text-muted mb-2">
                                            {{ number_format($stemImageUpload->getSize() / 1024, 1) }} KB
                                        </p>
                                        <button type="button"
                                                wire:click="removeStemImageUpload"
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="ri ri-delete-bin-line me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>


                    {{-- ── Answer Options ───────────────────────── --}}
                    @if (($activeQuestion['answer_category'] ?? '') !== 'open_text')

                        <div class="card shadow-sm border-0 mb-4">

                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-flex align-items-center
                                            justify-content-between flex-wrap gap-2">

                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-list-check-3 text-primary me-2"></i>
                                        Answer Options
                                        <span class="badge bg-primary ms-1">
                                            {{ count($activeQuestion['options'] ?? []) }}
                                        </span>
                                    </h6>

                                    <div class="d-flex align-items-center gap-2">

                                        {{-- Mode info badge --}}
                                        @if (($activeQuestion['answer_category'] ?? '') === 'multi_optional')
                                            <span class="badge bg-info-subtle text-info
                                                         border border-info-subtle small">
                                                <i class="ri ri-information-line me-1"></i>
                                                Multi-select — set weightage per correct answer
                                            </span>
                                        @endif

                                        <button type="button"
                                                wire:click="addOption"
                                                class="btn btn-outline-primary btn-sm"
                                                @if(count($activeQuestion['options'] ?? []) >= 8)
                                                    disabled
                                                @endif>
                                            <i class="ri ri-add-large-line me-1"></i>
                                            Add Option
                                        </button>

                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">

                                @error('activeQuestion.options')
                                    <div class="alert alert-warning py-2 small mb-3">
                                        <i class="ri ri-error-warning-fill me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error('options_min')
                                    <div class="alert alert-warning py-2 small mb-3">
                                        <i class="ri ri-error-warning-fill me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror

                                @foreach ($activeQuestion['options'] as $optIndex => $option)
                                    @php
                                        $optIsImage   = ($option['option_type'] ?? 'text') === 'image';
                                        $optIsCorrect = (bool) ($option['is_correct'] ?? false);
                                        $isMultiCat   = ($activeQuestion['answer_category'] ?? '')
                                                          === 'multi_optional';
                                    @endphp

                                    <div class="option-card rounded-3 border mb-3 overflow-hidden
                                                {{ $optIsCorrect ? 'border-success' : 'border-light' }}"
                                         wire:key="opt-{{ $optIndex }}">

                                        {{-- Option Header --}}
                                        <div class="option-header d-flex align-items-center
                                                    gap-2 px-3 py-2 flex-wrap
                                                    {{ $optIsCorrect ? 'bg-success-subtle' : 'bg-light' }}">

                                            {{-- Correct Toggle Button --}}
                                            <button type="button"
                                                    wire:click="toggleCorrect({{ $optIndex }})"
                                                    class="btn btn-sm {{ $optIsCorrect ? 'btn-success' : 'btn-outline-secondary' }}
                                                           rounded-circle p-0 flex-shrink-0"
                                                    style="width:32px;height:32px;"
                                                    title="{{ $optIsCorrect ? 'Mark as Incorrect' : 'Mark as Correct' }}">
                                                @if ($isMultiCat)
                                                    <i class="ri {{ $optIsCorrect
                                                        ? 'ri-checkbox-circle-fill'
                                                        : 'ri-checkbox-circle-line' }}"></i>
                                                @else
                                                    <i class="ri {{ $optIsCorrect
                                                        ? 'ri-record-circle-fill'
                                                        : 'ri-circle-line' }}"></i>
                                                @endif
                                            </button>

                                            {{-- Option Letter --}}
                                            <span class="badge bg-secondary fw-bold
                                                         d-inline-flex align-items-center
                                                         justify-content-center flex-shrink-0"
                                                  style="width:30px;height:30px;font-size:.85rem;">
                                                {{ chr(65 + $optIndex) }}
                                            </span>

                                            @if ($optIsCorrect)
                                                <span class="badge bg-success">
                                                    <i class="ri ri-check-fill me-1"></i>Correct
                                                </span>
                                            @endif

                                            {{-- Text / Image Toggle --}}
                                            <div class="d-flex align-items-center gap-1
                                                        bg-white rounded p-1 border ms-1">
                                                <button type="button"
                                                        wire:click="setOptionType({{ $optIndex }}, 'text')"
                                                        class="btn btn-sm py-0 px-2
                                                               {{ ! $optIsImage ? 'btn-primary' : 'btn-light' }}">
                                                    <i class="ri ri-text me-1"></i>Text
                                                </button>
                                                <button type="button"
                                                        wire:click="setOptionType({{ $optIndex }}, 'image')"
                                                        class="btn btn-sm py-0 px-2
                                                               {{ $optIsImage ? 'btn-primary' : 'btn-light' }}">
                                                    <i class="ri ri-image-line me-1"></i>Image
                                                </button>
                                            </div>

                                            <div class="ms-auto d-flex align-items-center gap-2">

                                                {{-- Weightage — only when correct --}}
                                                @if ($optIsCorrect)
                                                    <div class="d-flex align-items-center gap-1">
                                                        <label class="small text-muted mb-0 text-nowrap">
                                                            Weightage:
                                                        </label>
                                                        <input type="number"
                                                               wire:model.live="activeQuestion.options.{{ $optIndex }}.weightage"
                                                               class="form-control form-control-sm text-center"
                                                               style="width:70px;"
                                                               step="0.5" min="0" max="100"
                                                               placeholder="0">
                                                    </div>
                                                @else
                                                    {{-- Greyed out placeholder --}}
                                                    <div class="d-flex align-items-center gap-1"
                                                         title="Mark as correct first">
                                                        <label class="small text-muted mb-0 text-nowrap">
                                                            Weightage:
                                                        </label>
                                                        <input type="number"
                                                               class="form-control form-control-sm text-center"
                                                               style="width:70px;"
                                                               placeholder="0"
                                                               disabled>
                                                    </div>
                                                @endif

                                                {{-- Remove Option --}}
                                                <button type="button"
                                                        wire:click="removeOption({{ $optIndex }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Remove option">
                                                    <i class="ri ri-delete-bin-fill"></i>
                                                </button>

                                            </div>
                                        </div>

                                        {{-- Option Body --}}
                                        <div class="option-body p-3 bg-white">

                                            @if (! $optIsImage)
                                                {{-- TEXT MODE --}}
                                                @foreach ($languages as $langCode => $lang)
                                                    <div x-show="activeTab === '{{ $langCode }}'">
                                                        <input type="text"
                                                               wire:model="activeQuestion.options.{{ $optIndex }}.text.{{ $langCode }}"
                                                               class="form-control form-control-sm
                                                                      @error("activeQuestion.options.{$optIndex}.text.{$langCode}") is-invalid @enderror"
                                                               placeholder="{{ $lang['flag'] }} Option {{ chr(65 + $optIndex) }} — {{ $lang['label'] }}...">
                                                        @error("activeQuestion.options.{$optIndex}.text.{$langCode}")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endforeach

                                            @else
                                                {{-- IMAGE MODE --}}
                                                <div class="row g-3 align-items-start">

                                                    <div class="col-md-5"
                                                         x-data="{ dragging: false }"
                                                         @dragover.prevent="dragging = true"
                                                         @dragleave.prevent="dragging = false"
                                                         @drop.prevent="
                                                             dragging = false;
                                                             $refs.optFile_{{ $optIndex }}.files = $event.dataTransfer.files;
                                                             $refs.optFile_{{ $optIndex }}.dispatchEvent(new Event('change'))
                                                         ">

                                                        {{-- Existing --}}
                                                        @if (!empty($option['image_path']))
                                                            <div class="position-relative d-inline-block mb-2">
                                                                <img src="{{ Storage::url($option['image_path']) }}"
                                                                     alt="Option"
                                                                     class="img-thumbnail rounded"
                                                                     style="max-height:120px;object-fit:contain;">
                                                                <button type="button"
                                                                        wire:click="removeOptionImagePath({{ $optIndex }})"
                                                                        class="btn btn-danger btn-sm position-absolute
                                                                               top-0 end-0 m-1 rounded-circle p-0"
                                                                        style="width:22px;height:22px;line-height:1;">
                                                                    <i class="ri ri-close-line"
                                                                       style="font-size:.75rem;"></i>
                                                                </button>
                                                            </div>
                                                        @endif

                                                        {{-- Dropzone --}}
                                                        @if (empty($option['image_path']))
                                                            <div :class="dragging
                                                                         ? 'border-primary bg-primary bg-opacity-5'
                                                                         : 'border-secondary'"
                                                                 class="upload-dropzone border border-2
                                                                        border-dashed rounded-3 text-center p-3"
                                                                 style="cursor:pointer;min-height:90px;"
                                                                 @click="$refs.optFile_{{ $optIndex }}.click()">

                                                                <input type="file"
                                                                       x-ref="optFile_{{ $optIndex }}"
                                                                       wire:model="optionImages.{{ $optIndex }}"
                                                                       accept="image/jpeg,image/png,image/gif"
                                                                       class="d-none">

                                                                <div wire:loading
                                                                     wire:target="optionImages.{{ $optIndex }}"
                                                                     class="text-muted small">
                                                                    <span class="spinner-border spinner-border-sm"></span>
                                                                </div>
                                                                <div wire:loading.remove
                                                                     wire:target="optionImages.{{ $optIndex }}">
                                                                    <i class="ri ri-upload-cloud-2-line fs-4 text-muted"></i>
                                                                    <p class="mb-0 small text-muted mt-1">
                                                                        JPEG / PNG / GIF
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            @error("optionImages.{$optIndex}")
                                                                <div class="text-danger small mt-1">
                                                                    <i class="ri ri-error-warning-line me-1"></i>
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        @endif

                                                        {{-- Staged preview --}}
                                                        @if (!empty($optionImages[$optIndex]))
                                                            <div class="mt-2 d-flex align-items-start gap-2">
                                                                <img src="{{ $optionImages[$optIndex]->temporaryUrl() }}"
                                                                     alt="Preview"
                                                                     class="img-thumbnail rounded"
                                                                     style="max-height:80px;object-fit:contain;">
                                                                <button type="button"
                                                                        wire:click="removeOptionImageUpload({{ $optIndex }})"
                                                                        class="btn btn-outline-danger btn-sm">
                                                                    <i class="ri ri-close-line"></i>
                                                                </button>
                                                            </div>
                                                        @endif

                                                    </div>

                                                    {{-- Label --}}
                                                    <div class="col-md-7">
                                                        <label class="form-label small text-muted mb-1">
                                                            <i class="ri ri-text me-1"></i>
                                                            Label <span class="fw-normal">(optional)</span>
                                                        </label>
                                                        @foreach ($languages as $langCode => $lang)
                                                            <div x-show="activeTab === '{{ $langCode }}'"
                                                                 class="mb-1">
                                                                <input type="text"
                                                                       wire:model="activeQuestion.options.{{ $optIndex }}.text.{{ $langCode }}"
                                                                       class="form-control form-control-sm"
                                                                       placeholder="{{ $lang['flag'] }} Label in {{ $lang['label'] }}...">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            @endif

                                        </div>
                                    </div>

                                @endforeach

                                {{-- Summary --}}
                                <div class="d-flex gap-3 pt-2 border-top mt-1">
                                    <small class="text-muted">
                                        <i class="ri ri-check-circle-fill text-success me-1"></i>
                                        Correct:
                                        <strong>
                                            {{ collect($activeQuestion['options'])->where('is_correct', true)->count() }}
                                        </strong>
                                    </small>
                                    <small class="text-muted">
                                        <i class="ri ri-list-ordered-2 me-1"></i>
                                        Total:
                                        <strong>{{ count($activeQuestion['options']) }}</strong>
                                    </small>
                                </div>

                            </div>
                        </div>

                    @endif
                    {{-- /options --}}


                    {{-- ── Explanation ──────────────────────────── --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-lightbulb-fill text-warning me-2"></i>
                                Explanation
                                <span class="text-muted fw-normal small ms-1">(Optional)</span>
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            @foreach ($languages as $langCode => $lang)
                                <div x-show="activeTab === '{{ $langCode }}'" x-cloak>
                                    <textarea
                                        wire:model="activeQuestion.explanation.{{ $langCode }}"
                                        class="form-control"
                                        rows="3"
                                        placeholder="{{ $lang['flag'] }} Explain the answer in {{ $lang['label'] }}...">
                                    </textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                {{-- /left --}}


                {{-- ── Right Column ─────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Group Context Card --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-folder-info-line text-primary me-2"></i>
                                Group Context
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group list-group-flush">

                                <li class="list-group-item px-0 d-flex
                                           justify-content-between align-items-center">
                                    <span class="small text-muted">Code</span>
                                    <code class="small">{{ $group_code }}</code>
                                </li>

                                <li class="list-group-item px-0 d-flex
                                           justify-content-between align-items-center">
                                    <span class="small text-muted">Category</span>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ ucfirst($questions_category) }}
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex
                                           justify-content-between align-items-center">
                                    <span class="small text-muted">Questions so far</span>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ count($questionsList) }}
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex
                                           justify-content-between align-items-center">
                                    <span class="small text-muted">Total Marks</span>
                                    <span class="badge bg-success rounded-pill">
                                        {{ collect($questionsList)->sum('marks') }}
                                    </span>
                                </li>

                                @foreach ($languages as $langCode => $lang)
                                    @if (!empty($group_content['title'][$langCode]))
                                        <li class="list-group-item px-0">
                                            <span class="small text-muted d-block mb-1">
                                                {{ $lang['flag'] }} Title
                                            </span>
                                            <span class="small fw-medium">
                                                {{ $group_content['title'][$langCode] }}
                                            </span>
                                        </li>
                                    @endif
                                @endforeach

                            </ul>
                        </div>
                    </div>

                    {{-- Save Card --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-newspaper-fill text-secondary me-2"></i>
                                Save Question
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted mb-0">
                                After saving, you'll return to the group view where
                                you can add more questions.
                            </p>
                        </div>
                        <div class="card-footer bg-white p-3 d-flex flex-column gap-2">
                            <button type="button"
                                    wire:click="saveQuestion"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary w-100">
                                <span wire:loading wire:target="saveQuestion">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Saving…
                                </span>
                                <span wire:loading.remove wire:target="saveQuestion">
                                    <i class="ri ri-save-line me-1"></i>
                                    Save Question
                                </span>
                            </button>

                            <button type="button"
                                    wire:click="cancelQuestion"
                                    class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="ri ri-arrow-left-line me-1"></i>
                                Cancel
                            </button>
                        </div>
                    </div>

                    {{-- Language Guide --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-global-line text-success me-2"></i>
                                Active Languages
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @foreach ($languages as $langCode => $lang)
                                <div class="mb-2">
                                    <button type="button"
                                            @click="activeTab = '{{ $langCode }}'"
                                            class="btn btn-sm w-100 text-start"
                                            :class="activeTab === '{{ $langCode }}'
                                                    ? 'btn-primary'
                                                    : 'btn-light border'">
                                        {{ $lang['flag'] }} {{ $lang['label'] }}
                                        <span class="badge bg-white text-dark ms-1 small">
                                            {{ strtoupper($langCode) }}
                                        </span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                {{-- /right --}}

            </div>
        </form>

    @endif
    {{-- /question_form --}}

</div>
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/highlight/highlight.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/table.css') }}">

    <style>
        .ql-container {
            min-height: 150px;
            height: auto !important;
        }

        .ql-editor {
            min-height: 150px;
            overflow-y: visible;
        }

        .ql-editor table,
        .ql-editor td,
        .ql-editor th {
            border: 1px solid #000 !important;
            border-collapse: collapse;
        }

        .ql-editor img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .upload-dropzone {
            transition: border-color .2s, background .2s;
        }

        .upload-dropzone:hover {
            border-color: var(--bs-primary) !important;
            background: rgba(var(--bs-primary-rgb), .03);
        }
    </style>
@endpush
@push('script')
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/table.js') }}"></script>
    <script>
        Quill.register({
            'modules/table-better': QuillTableBetter
        }, true);

        const fullToolbar = [
            [{
                    font: []
                },
                {
                    size: []
                }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            ['table-better'],
            [{
                    color: []
                },
                {
                    background: []
                }
            ],
            [{
                    script: 'super'
                },
                {
                    script: 'sub'
                }
            ],
            [{
                    header: '1'
                },
                {
                    header: '2'
                },
                'blockquote',
                'code-block'
            ],
            [{
                    list: 'ordered'
                },
                {
                    indent: '-1'
                },
                {
                    indent: '+1'
                }
            ],
            [{
                direction: 'rtl'
            }, {
                align: []
            }],
            ['link', 'video', 'formula', 'table'],

            ['clean']
        ];
    </script>
@endpush
