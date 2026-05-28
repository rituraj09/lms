<?php

use App\Models\Master\Designation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.backend')]
class extends Component
{
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = "New Designation";
    public ?int $eventID = null;


     // Form Fields
    public ?string $name = null;
    public ?string $description = null;


    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->title = "New Designation";
        $this->createForm = 1;
    }
      #[On('edit')]
    public function edit($id)
    {
        $this->resetForm();

        $designation = Designation::findOrFail($id);

        $this->is_edit = true;
        $this->eventID = $id;
        $this->createForm = 1;
        $this->title = "Edit Designation";

        $this->name = $designation->name;
        $this->description = $designation->description;
    }

     protected function rules(): array
    {
        return [

            'name' => 'required|string|max:100',
        ];
    }
      protected array $messages = [

        'name.required' => 'Designation name is required.',
    ];

     #[On('delete')]
    public function delete($id): void
    {
        try {
            $designation = Designation::findOrFail($id);

            $designation->delete();

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Designation deleted successfully!'
            );

            $this->dispatch('refresh-table');

        } catch (\Exception $e) {

            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function updatedName(): void
    {
        $this->name = Str::title($this->name);
    }
    protected function resetForm()
    {  $this->reset([
            'name',
            'description'

        ]);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->eventID = null;
    }
     public function submit(): void
    {
        $validated = $this->validate();
        DB::beginTransaction();

        try {

            if ($this->is_edit){
                $designation = Designation::findOrFail($this->eventID);
                $designation->update([
                    'name' => Str::title($this->name),
                    'description' => $this->description,
                ]);
            } else {
                Designation::create([
                    'name' => Str::title($this->name),
                    'description' => $this->description,
                ]);
            }
            DB::commit();
                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: 'Designation has been created/updated successfully!'
            );
            $this->resetForm();
            $this->createForm = 0;
            $this->dispatch('refresh-table');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }
};

?>
<div>

    @if($createForm == 0)
        <livewire:datatable
            model="App\Models\Master\Designation"
            title="Designations"
            :new-entry="true"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'description', 'label' => 'Description'],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]"
            :actions="[
                ['label'=>'Edit','icon'=>'icon-base ri ri-edit-line','event'=>'edit','class'=>'btn-outline-primary'],
                ['label'=>'Delete','icon'=>'icon-base ri ri-delete-bin-4-line','event'=>'delete','class'=>'btn-outline-danger','confirm'=>true],
            ]"
        />

    @elseif($createForm == 1)

           <div class="row">
            <div class="col-md-6">

                <div class="card mb-6">

                    <div class="card-header">
                        <h5 class="text-center mb-0">
                            {{ $title }}
                        </h5>
                    </div>

                    <hr>

                    <div class="card-body">

                        <form wire:submit.prevent="submit">
                            <div class="row g-5">
                                    {{-- Designation --}}
                                <div class="row">
                                    <div class="col-md-12 mb-3">

                                        <div class="form-floating form-floating-outline">

                                            <input type="text"
                                                id="name"
                                                placeholder=" "
                                                wire:model.blur="name"
                                                maxlength="100"
                                                class="form-control @error('name') is-invalid @enderror">

                                            <label for="name">
                                                Designation Name
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                        @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                    </div>
                                </div>
                                <div class="row">
                                {{-- Description --}}
                                    <div class="col-md-12">

                                        <div class="form-floating form-floating-outline">

                                            <textarea id="description"
                                                    placeholder=" "
                                                    wire:model.blur="description"
                                                    style="height: 100px"
                                                    class="form-control @error('description') is-invalid @enderror"></textarea>

                                            <label for="description">
                                                Description
                                            </label>

                                        </div>

                                        @error('description')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                    </div>
                                </div>

                            </div>

                            {{-- Buttons --}}
                            <div class="mt-6 text-end">

                                <button type="submit"
                                        class="btn btn-primary me-2">

                                    Save Changes

                                </button>

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        wire:click="$set('createForm', 0)">

                                    Cancel

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    @endif
    <div wire:loading>
        @include('utilities.backdrop')
    </div>

</div>
