<?php

use App\Models\Master\Organisation;
use App\Models\Master\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[\Livewire\Attributes\Layout('layouts.backend')]
class extends Component {

    use WithFileUploads;

    public int $createForm = 0;

    public bool $is_edit = false;

    public string $title = "Organisation";

    // Form Fields
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $organisation_type = null;
    public ?int $state_id = null;
    public ?string $pincode = null;
    public ?string $website = null;

    public array $social_links = [];

    // Collections
    public $states;
    public $organisation_types;

    // Photo Upload
    public $photo;

    public ?string $existing_photo = null;

    protected function rules()
    {
        return [

            'name' => 'required|string|max:100',

            'organisation_type' => 'required',

            'phone' => 'nullable|digits:10',

            'email' => 'nullable|email|max:100',

            'address' => 'nullable|string|max:500',

            'state_id' => 'required|integer',

            'pincode' => 'nullable|digits:6',

            'website' => 'nullable|url|max:255',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:800',
        ];
    }

    protected $messages = [

        'name.required' => 'Organisation name is required.',

        'organisation_type.required' => 'Please select organisation type.',

        'state_id.required' => 'Please select a state.',

        'phone.digits' => 'Phone number must be 10 digits.',

        'email.email' => 'Please enter a valid email address.',

        'pincode.digits' => 'Pincode must be 6 digits.',

        'website.url' => 'Please enter a valid website URL.',

        'photo.image' => 'Uploaded file must be an image.',

        'photo.max' => 'Logo size must not exceed 800KB.',
    ];

    public function mount()
    {
        $this->states = State::all();

        $this->organisation_types =
            \App\Helper\Globals::ORGANISATION_TYPES;
    }

    #[On('new-entry')]
    public function newEntry()
    {
        $this->createForm = 1;
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function updatedName()
    {
        $this->name = Str::title($this->name);
    }

    public function submit()
    {
        $validated = $this->validate();

        DB::beginTransaction();

        try {

            $logoPath = $this->existing_photo;

            // Upload Logo
            if ($this->photo) {

                $logoPath = $this->photo->store(
                    'organisation/logo',
                    'public'
                );
            }

            Organisation::create([

                'name' => Str::title($this->name),

                'phone' => $this->phone,

                'email' => $this->email,

                'address' => $this->address,

                'organisation_type' => $this->organisation_type,

                'state_id' => $this->state_id,

                'pincode' => $this->pincode,

                'website' => $this->website,

                'social_links' => json_encode($this->social_links),

                'logo_path' => $logoPath,
            ]);

            DB::commit();

            session()->flash(
                'success',
                'Organisation created successfully.'
            );

            $this->resetForm();

            $this->createForm = 0;

            $this->dispatch('refreshDatatable');

        } catch (\Exception $e) {

            DB::rollBack();

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'phone',
            'email',
            'address',
            'organisation_type',
            'state_id',
            'pincode',
            'website',
            'social_links',
            'photo',
            'existing_photo',
        ]);

        $this->social_links = [];
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

                ['key' => 'logo_path', 'label' => 'Logo', 'type' => 'image'],
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'organisation_type', 'label' => 'Organisation Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'state.name', 'label' => 'State', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]"
            :actions="[
                ['label'=>'Edit','icon'=>'icon-base ri ri-edit-line','event'=>'edit','class'=>'btn-outline-primary'],
                ['label'=>'Delete','icon'=>'icon-base ri ri-delete-bin-4-line','event'=>'delete','class'=>'btn-outline-danger','confirm'=>true],
            ]"
        />

    @elseif($createForm == 1)

        <div class="row">
            <div class="col-md-12">

                <div class="card mb-6">

                    <div class="card-header">
                        <h5 class="text-center mb-0">
                            {{ $title }}
                        </h5>
                    </div>

                    <hr>

                    <div class="card-body">

                        <form wire:submit.prevent="submit">

                            {{-- Top Validation Summary --}}
                            @if ($errors->any())

                                <div class="alert alert-danger">

                                    <h6 class="mb-2">
                                        Please fix the following errors:
                                    </h6>

                                    <ul class="mb-0">

                                        @foreach ($errors->all() as $error)

                                            <li>{{ $error }}</li>

                                        @endforeach

                                    </ul>

                                </div>

                            @endif

                            <div class="row g-5">

                                {{-- Name --}}
                                <div class="col-md-6">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               id="name"
                                               placeholder=" "
                                               wire:model.blur="name"
                                               maxlength="100"
                                               class="form-control @error('name') is-invalid @enderror">

                                        <label for="name">
                                            Organisation Name
                                            <span class="text-danger">*</span>
                                        </label>

                                    </div>

                                    @error('name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Organisation Type --}}
                                <div class="col-md-6">

                                    <div class="form-floating form-floating-outline">

                                        <select id="organisation_type"
                                                wire:model="organisation_type"
                                                class="form-select @error('organisation_type') is-invalid @enderror">

                                            <option value="">
                                                Select Organisation Type
                                            </option>

                                            @foreach($organisation_types as $type)

                                                <option value="{{ $type }}">
                                                    {{ $type }}
                                                </option>

                                            @endforeach

                                        </select>

                                        <label for="organisation_type">
                                            Organisation Type
                                            <span class="text-danger">*</span>
                                        </label>

                                    </div>

                                    @error('organisation_type')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Phone --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               id="phone"
                                               placeholder=" "
                                               wire:model.blur="phone"
                                               maxlength="10"
                                               class="form-control @error('phone') is-invalid @enderror">

                                        <label for="phone">
                                            Phone Number
                                        </label>

                                    </div>

                                    @error('phone')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Email --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="email"
                                               id="email"
                                               placeholder=" "
                                               wire:model.blur="email"
                                               class="form-control @error('email') is-invalid @enderror">

                                        <label for="email">
                                            Email Address
                                        </label>

                                    </div>

                                    @error('email')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Website --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               id="website"
                                               placeholder=" "
                                               wire:model.blur="website"
                                               class="form-control @error('website') is-invalid @enderror">

                                        <label for="website">
                                            Website
                                        </label>

                                    </div>

                                    @error('website')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- State --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <select id="state_id"
                                                wire:model="state_id"
                                                class="form-select @error('state_id') is-invalid @enderror">

                                            <option value="">
                                                Select State
                                            </option>

                                            @foreach($states as $state)

                                                <option value="{{ $state->id }}">
                                                    {{ $state->name }}
                                                </option>

                                            @endforeach

                                        </select>

                                        <label for="state_id">
                                            State
                                            <span class="text-danger">*</span>
                                        </label>

                                    </div>

                                    @error('state_id')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Pincode --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               id="pincode"
                                               placeholder=" "
                                               wire:model.blur="pincode"
                                               maxlength="6"
                                               class="form-control @error('pincode') is-invalid @enderror">

                                        <label for="pincode">
                                            Pincode
                                        </label>

                                    </div>

                                    @error('pincode')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Address --}}
                                <div class="col-md-12">

                                    <div class="form-floating form-floating-outline">

                                        <textarea id="address"
                                                  placeholder=" "
                                                  wire:model.blur="address"
                                                  style="height: 100px"
                                                  class="form-control @error('address') is-invalid @enderror"></textarea>

                                        <label for="address">
                                            Address
                                        </label>

                                    </div>

                                    @error('address')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                {{-- Facebook --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               placeholder=" "
                                               wire:model.blur="social_links.facebook"
                                               class="form-control">

                                        <label>
                                            Facebook Link
                                        </label>

                                    </div>

                                </div>

                                {{-- Instagram --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               placeholder=" "
                                               wire:model.blur="social_links.instagram"
                                               class="form-control">

                                        <label>
                                            Instagram Link
                                        </label>

                                    </div>

                                </div>

                                {{-- Twitter --}}
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <input type="text"
                                               placeholder=" "
                                               wire:model.blur="social_links.twitter"
                                               class="form-control">

                                        <label>
                                            Twitter/X Link
                                        </label>

                                    </div>

                                </div>

                                {{-- Logo Upload --}}
                                <div class="col-md-12">

                                    <div class="d-flex align-items-start gap-6">

                                        <img
                                            src="
                                            @if($photo)
                                                {{ $photo->temporaryUrl() }}
                                            @elseif(!is_null($existing_photo))
                                                {{ asset('storage/'.$existing_photo) }}
                                            @else
                                                {{ asset('assets/img/avatars/2.png') }}
                                            @endif
                                            "
                                            alt="logo"
                                            class="d-block w-px-100 h-px-100 rounded-4 object-fit-cover">

                                        <div>

                                            <label for="upload"
                                                   class="btn btn-primary mb-3">

                                                Upload Logo

                                                <input type="file"
                                                       id="upload"
                                                       hidden
                                                       accept="image/png,image/jpeg,image/jpg"
                                                       wire:model="photo">

                                            </label>

                                            <div>
                                                Allowed JPG, JPEG or PNG.
                                                Max size 800KB
                                            </div>

                                            <div wire:loading
                                                 wire:target="photo"
                                                 class="text-primary mt-2">

                                                Uploading...

                                            </div>

                                            @error('photo')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror

                                        </div>

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
