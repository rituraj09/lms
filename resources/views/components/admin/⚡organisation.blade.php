<?php

use App\Models\Master\District;
use App\Models\Master\Organisation;
use App\Models\Master\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.backend')]
class extends Component {

    use WithFileUploads;

    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = "New Organisation";
    public ?int $eventID = null;

    // Form Fields
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $organisation_type = null;
    public ?int $state_id = null;
    public ?int $district_id = null;
    public ?string $pincode = null;
    public ?string $website = null;

    public array $social_links = [];

    // Collections
    public $states;
    public $districts;
    public $organisation_types;

    // Photo Upload
    public $photo;
    public ?string $existing_photo = null;



    protected function rules(): array
    {
        return [

            'name' => 'required|string|max:100',

            'organisation_type' => 'required',

            'phone' => 'nullable|digits:10',

            'email' => 'nullable|email|max:100',

            'address' => 'nullable|string|max:500',

            'state_id' => 'required|integer|exists:states,id',

            'district_id' => 'required|integer|exists:districts,id',

            'pincode' => 'nullable|digits:6',

            'website' => 'nullable|url|max:255',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,tiff|max:800',
        ];
    }

    protected array $messages = [

        'name.required' => 'Organisation name is required.',

        'organisation_type.required' => 'Please select organisation type.',

        'state_id.required' => 'Please select a state.',

        'district_id.required' => 'Please select a district.',

        'phone.digits' => 'Phone number must be 10 digits.',

        'email.email' => 'Please enter a valid email address.',

        'pincode.digits' => 'Pincode must be 6 digits.',

        'website.url' => 'Please enter a valid website URL.',

        'photo.image' => 'Uploaded file must be an image.',

        'photo.max' => 'Logo size must not exceed 800KB.',
    ];

    public function mount(): void
    {
        $this->states = State::all();
        $this->districts = collect();
        $this->organisation_types =
            \App\Helper\Globals::ORGANISATION_TYPES;
    }

    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->title = "New Organisation";
        $this->createForm = 1;
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetForm();

        $organisation = Organisation::findOrFail($id);

        $this->is_edit = true;
        $this->eventID = $id;
        $this->createForm = 1;
        $this->title = "Edit Organisation";

        $this->name = $organisation->name;
        $this->phone = $organisation->phone;
        $this->email = $organisation->email;
        $this->address = $organisation->address;
        $this->organisation_type = $organisation->organisation_type;
        $this->state_id = $organisation->state_id;

        $this->districts = District::byState($this->state_id)->get();
        $this->district_id = $organisation->district_id;

        $this->pincode = $organisation->pincode;
        $this->website = $organisation->website;

        // $this->social_links = json_decode($organisation->social_links, true) ?? [];
        $this->social_links = $organisation->social_links ?? [];

        $this->existing_photo = $organisation->logo_path;
    }

    #[On('delete')]
    public function delete($id): void
    {
        try {
            $organisation = Organisation::findOrFail($id);

            // delete logo if exists
            if ($organisation->logo_path) {
                Storage::disk('public')->delete($organisation->logo_path);
            }

            $organisation->delete();

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Organisation deleted successfully!'
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

    public function updatedStateId(): void
    {
        $this->districts = District::byState($this->state_id)->get();
    }

    public function submit(): void
    {
        $validated = $this->validate();

        DB::beginTransaction();
        $logoPath = null;
        try {
            if (!is_null($this->photo) && $this->photo->isValid()) {
                $image = ImageManager::usingDriver(Driver::class)
                    ->decode($this->photo);
                $image->cover(200, 200);

                if ($this->existing_photo) {
                    Storage::disk('public')->delete($this->existing_photo);
                }
                $filename = Str::random(8) . '-' . time() . '.' . $this->photo->extension();
                $logoPath = 'Organisation/' . $filename;
                $image->save(storage_path('app/public/' . $logoPath));
            }
            if ($this->is_edit){
                $organisation = Organisation::findOrFail($this->eventID);
                $organisation->update([
                    'name' => Str::title($this->name),
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'address' => $this->address,
                    'organisation_type' => $this->organisation_type,
                    'state_id' => $this->state_id,
                    'district_id' => $this->district_id,
                    'pincode' => $this->pincode,
                    'website' => $this->website,
                    'social_links' => $this->social_links,
                    'logo_path' => $logoPath ?? $this->existing_photo,
                ]);

            } else {
                Organisation::create([
                    'name' => Str::title($this->name),
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'address' => $this->address,
                    'organisation_type' => $this->organisation_type,
                    'state_id' => $this->state_id,
                    'district_id' => $this->district_id,
                    'pincode' => $this->pincode,
                    'website' => $this->website,
                    'social_links' => $this->social_links,
                    'logo_path' => $logoPath,
                ]);
            }


            DB::commit();
            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Organisation has been created/updated successfully!'
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

    protected function resetForm()
    {
        $this->reset([
            'name',
            'phone',
            'email',
            'address',
            'organisation_type',
            'state_id',
            'district_id',
            'pincode',
            'website',
            'social_links',
            'photo',
            'existing_photo',
        ]);
        $this->districts = collect();
        $this->resetErrorBag();
        $this->resetValidation();
        $this->social_links = [];
        $this->is_edit = false;
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
                ['key' => 'phone', 'label' => 'Phone', 'sortable' => true, 'searchable' => true],
                ['key' => 'state.name', 'label' => 'State', 'sortable' => true, 'searchable' => true],
                ['key' => 'district.name', 'label' => 'District', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]"
            :actions="[
                ['label'=>'View','icon'=>'icon-base ri ri-focus-2-line','event'=>'viewOrganisation','class'=>'btn-outline-success'],
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
                            <div class="row g-5">
                                <div class="d-flex align-items-start align-items-sm-center gap-6">
                                    <img
                                        src="@if($photo)
                                                {{ $photo->temporaryUrl() }}
                                            @elseif(!is_null($existing_photo))
                                                {{ asset('storage/'.$existing_photo) }}
                                            @else
                                                {{ asset('assets/img/avatars/2.png') }}
                                            @endif"
                                        alt="photo"
                                        class="d-block w-px-100 h-px-100 rounded-4" id="uploadedAvatar">
                                    <div class="button-wrapper">
                                        <label for="upload"
                                               class="btn btn-primary me-3 mb-4 waves-effect waves-light"
                                               tabindex="0">
                                            <span class="d-none d-sm-block">Upload new logo</span>
                                            <i class="icon-base ri ri-upload-2-line d-block d-sm-none"></i>
                                            <input type="file" id="upload" class="account-file-input" hidden=""
                                                   accept="image/png, image/jpeg" wire:model.live="photo">
                                        </label>
                                        <div>Allowed JPG, GIF or PNG. Max size of 800K</div>
                                    </div>
                                    @error('photo')
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">{{$message}}</div>
                                    @enderror
                                </div>
                                <hr>
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
                                                wire:model.live="state_id"
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
                                <div class="col-md-4">

                                    <div class="form-floating form-floating-outline">

                                        <select id="district_id"
                                                wire:model="district_id"
                                                class="form-select @error('district_id') is-invalid @enderror">

                                            <option value="">
                                                Select District
                                            </option>

                                            @foreach($districts as $v)

                                                <option value="{{ $v->id }}">
                                                    {{ $v->name }}
                                                </option>

                                            @endforeach

                                        </select>

                                        <label for="district_id">
                                            District
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
    @livewire('modal.form-modal')
    {{-- View Modal --}}

    <div wire:loading>
        @include('utilities.backdrop')
    </div>

</div>
