<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.backend')] class extends Component {
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = 'New Question';
    public ?int $eventID = null;
};
?>

<div>
    @if ($createForm == 0)
        <livewire:datatable model="App\Models\EvaluationMaster\Question" title="Questions" :new-entry="true"
            :columns="[
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true],
                ['key' => 'question_type.name', 'label' => 'Question Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'admin_notes', 'label' => 'Admin Notes', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
                [
                    'label' => 'View',
                    'icon' => 'icon-base ri ri-focus-2-line',
                    'event' => 'viewOrganisation',
                    'class' => 'btn-outline-success',
                ],
                [
                    'label' => 'Edit',
                    'icon' => 'icon-base ri ri-edit-line',
                    'event' => 'edit',
                    'class' => 'btn-outline-primary',
                ],
                [
                    'label' => 'Delete',
                    'icon' => 'icon-base ri ri-delete-bin-4-line',
                    'event' => 'delete',
                    'class' => 'btn-outline-danger',
                    'confirm' => true,
                ],
            ]" />
    @endif
    <div wire:loading>
        @include('utilities.backdrop')
    </div>
</div>
