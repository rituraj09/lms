<?php

use App\Models\Master\District;
use App\Models\Master\State;
use Livewire\Attributes\On;
use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts.backend')]
class extends Component {
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = "Organisation";

    //Form fields
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $organisation_type = null;
    public ?int $state_id = null;
    public ?int $district_id = null;
    public ?string $pincode = null;
    public ?string $website = null;
    public ?array $social_links = null;
    public ?string $logo_path = null;

    //collections
    public $states;
    public $districts;
    public $organisation_types;

    public function mount()
    {
        $this->states = State::all();
        $this->organisation_types = \App\Helper\Globals::ORGANISATION_TYPES;
    }

    #[On('new-entry')]
    public function newEntry()
    {
        $this->createForm = 1;
    }

    public function updatedName()
    {
        $this->name = Str::title($this->name);
    }
};
?>

<div>
    @if($createForm == 0)

        <livewire:datatable
            model="App\Models\Master\Organisation"
            title="Organisations"
            :new-entry="true"
            :columns="[
                ['key' => 'name',  'label' => 'Name',  'sortable' => true, 'searchable' => true],
                ['key' => 'actions','label' => 'Actions','type' => 'actions'],
             ]"
            :actions="[
                ['label'=>'Edit','icon' => 'icon-base ri ri-edit-line',   'event' => 'edit', 'class' => 'btn-outline-primary'],
                ['label'=>'Delete','icon' => 'icon-base ri ri-delete-bin-4-line', 'event' => 'delete', 'class' => 'btn-outline-danger','confirm'=>true],
        ]"
        />
    @elseif($createForm == 1)

        <div class="row fv-plugins-icon-container">
            <div class="col-md-12">
                <div class="nav-align-top">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="text-center">{{ $title }}</h5>
                        </div>
                        <hr>
                        <div class="card-body pt-0">
                            <form id="formAccountSettings" wire:submit.prevent="submit"
                                  class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                <div class="row mt-1 g-5">
                                    <div class="col-md-4 form-control-validation fv-plugins-icon-container">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                                   id="name" wire:model.blur="name" autofocus maxlength="50">
                                            <label for="name">Organisation Name <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        @error('name')
                                        <div
                                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">{{$message}}</div
                                        >@enderror
                                    </div>
                                </div>
                                <div class="mt-6 btn-group float-end">
                                    <button type="submit"
                                            class="btn btn-primary me-3 waves-effect waves-light">Save
                                        changes
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary waves-effect"
                                            wire:click="$set('createForm', 0)">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div wire:loading>
        @include('utilities.backdrop')
    </div>
</div>
