<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\EvaluationMaster\AgeGroup;

new #[Layout('layouts.backend')] class extends Component {
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = 'New Age Group';
    public ?int $eventID = null;

    // Form Fields
    public ?string $name = null;
    public ?string $min_age = null;
    public ?string $max_age = null;
};
?>

<div>
    @if ($createForm == 0)
        <livewire:datatable model="App\Models\EvaluationMaster\AgeGroup" title="Age Groups" :new-entry="true"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'min_age', 'label' => 'Minimum Age'],
                ['key' => 'max_age', 'label' => 'Maximum Age'],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
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
</div>
