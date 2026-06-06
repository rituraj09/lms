{{--
    question/question-form.blade.php
    Create / Edit form view.
    Included by question-manager.blade.php when $view === 1.
--}}
<div class="question-form-wrapper" x-data="{ activeTab: '{{ array_key_first($languages) }}' }">

    {{-- ══ Page Header ══════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="ri ri-questionnaire-fill text-primary me-2"></i>
                {{ $isEditing ? 'Edit Question' : 'Create New Question' }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="#" wire:click.prevent="$set('view', 0)">Questions</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $isEditing ? 'Edit' : 'Create' }}
                    </li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            wire:click="$set('view', 0)">
            <i class="ri ri-arrow-left-line me-1"></i> Back to List
        </button>
    </div>

    {{-- ══ Flash Messages ═══════════════════════════════════ --}}
    @include('livewire.evaluation-master.partials.alerts')

    {{-- ══ Form ════════════════════════════════════════════ --}}
    <form wire:submit.prevent>
        <div class="row g-4">

            {{-- ── Left Column ───────────────────────────── --}}
            <div class="col-lg-8">

                {{-- Basic Information --}}
                @include('livewire.evaluation-master.question.form.basic-info')

                {{-- Question Stem --}}
                @include('livewire.evaluation-master.question.form.stem')

                {{-- Answer Options --}}
                @include('livewire.evaluation-master.question.form.options')

                {{-- Explanation --}}
                @include('livewire.evaluation-master.question.form.explanation')

            </div>

            {{-- ── Right Column ──────────────────────────── --}}
            <div class="col-lg-4">

                {{-- Scoring Settings --}}
                @include('livewire.evaluation-master.question.form.scoring')

                {{-- Admin Notes + Preview Button --}}
                @include('livewire.evaluation-master.question.form.admin-notes')

            </div>

        </div>
    </form>
</div>

