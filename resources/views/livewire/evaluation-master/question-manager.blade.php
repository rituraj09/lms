{{--
    question-manager.blade.php
    Routing shell — delegates each view to a dedicated partial.
--}}
<div>

    {{-- ── VIEW 0: Datatable List ─────────────────────────── --}}
    @if ($view === 0)
        <livewire:datatable
            model="App\Models\EvaluationMaster\Question"
            title="Questions"
            :new-entry="true"
            :columns="[
                ['key' => 'code',               'label' => 'Code',          'sortable' => true, 'searchable' => true],
                ['key' => 'question_type.name', 'label' => 'Question Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'stem_text', 'label' => 'Question Stem', 'searchable' => true],
                ['key' => 'admin_notes',        'label' => 'Admin Notes',   'sortable' => true, 'searchable' => true],
                ['key' => 'actions',            'label' => 'Actions',       'type'     => 'actions'],
            ]"
            :actions="[
                ['label' => 'View',   'icon' => 'icon-base ri ri-focus-2-line',      'event' => 'viewQuestion', 'class' => 'btn-outline-success'],
                ['label' => 'Edit',   'icon' => 'icon-base ri ri-edit-line',          'event' => 'edit',         'class' => 'btn-outline-primary'],
                ['label' => 'Delete', 'icon' => 'icon-base ri ri-delete-bin-4-line',  'event' => 'delete',       'class' => 'btn-outline-danger', 'confirm' => true],
            ]"
        />
    @endif

    {{-- ── VIEW 1: Create / Edit Form ────────────────────── --}}
    @if ($view === 1)
        @include('livewire.evaluation-master.partials.question-form')
    @endif

    {{-- ── VIEW 3: Preview & Publish ─────────────────────── --}}
    @if ($view === 3)
        @include('livewire.evaluation-master.partials.question-preview')
    @endif

    {{-- ── Global Loading Backdrop ───────────────────────── --}}
    <div wire:loading>
        @include('utilities.backdrop')
    </div>

</div>
