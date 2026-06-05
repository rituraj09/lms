{{-- question-set/manager.blade.php — Step 1 router --}}
<div>

    {{-- ── VIEW 0: Datatable ───────────────────────────────── --}}
    @if ($view === 0)
        <livewire:datatable
            model="App\Models\EvaluationMaster\QuestionSet"
            title="Question Sets"
            :new-entry="true"
            :columns="[
                ['key' => 'code',              'label' => 'Code',       'sortable' => true,  'searchable' => true],
                ['key' => 'title',             'label' => 'Title',      'sortable' => true,  'searchable' => true],
                ['key' => 'question_set_type', 'label' => 'Type',       'sortable' => true],
                ['key' => 'total_questions',   'label' => 'Questions',  'sortable' => true],
                ['key' => 'status',            'label' => 'Status',     'sortable' => true],
                ['key' => 'actions',           'label' => 'Actions',    'type'     => 'actions'],
            ]"
            :actions="[
                ['label' => 'Builder', 'icon' => 'icon-base ri ri-layout-grid-line', 'event' => 'openBuilder', 'class' => 'btn-outline-success'],
                ['label' => 'Edit',    'icon' => 'icon-base ri ri-edit-line',         'event' => 'edit',        'class' => 'btn-outline-primary'],
                ['label' => 'Delete',  'icon' => 'icon-base ri ri-delete-bin-4-line', 'event' => 'delete',      'class' => 'btn-outline-danger', 'confirm' => true],
            ]"
        />
    @endif

    {{-- ── VIEW 1: Set Details Form ───────────────────────── --}}
    @if ($view === 1)
        @include('livewire.evaluation-master.question-set.form')
    @endif

    <div wire:loading>@include('utilities.backdrop')</div>
</div>
