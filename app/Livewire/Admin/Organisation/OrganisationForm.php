<?php
// app/Livewire/Admin/Organisation/OrganisationForm.php


namespace App\Livewire\Admin\Organisation;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Master\Organisation;
use App\Models\Master\State;
use App\Models\Master\District;
use App\Models\Master\OrganisationType;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use App\Traits\WithOrganisationAccess;

#[Layout('layouts.backend')]
class OrganisationForm extends Component
{
    use WithFileUploads;

    public ?Organisation $organisation = null;
    public bool $isEditing = false;

    // Form Fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $website = '';
    public string $description = '';
    public int    $organisation_type_id = 0;
    public string $status = 'active';
    public int    $max_students = 0;
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public int    $state_id = 0;
    public int    $district_id = 0;
    public string $country = '';
    public string $postal_code = '';
    public string $contact_person = '';
    public string $contact_person_phone = '';
    public string $contact_person_email = '';
    public string $subscription_start = '';
    public string $subscription_end = '';

    public $logo = null;
    public $banner = null;
    public ?string $existingLogo = null;
    public ?string $existingBanner = null;

    // Dropdowns
    public $states = [];
    public $districts = [];
    public $organisationTypes = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'organisation_type_id' => 'required|integer|exists:organisation_types,id',
            'status' => 'required|in:active,inactive,suspended',
            'max_students' => 'required|integer|min:0',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state_id' => 'nullable|integer|exists:states,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date|after_or_equal:subscription_start',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->organisationTypes = OrganisationType::active()->get();
        $this->states = State::all();

        if ($id) {
            $this->organisation = Organisation::findOrFail($id);
            $this->isEditing = true;

            $this->fill([
                'name' => $this->organisation->name,
                'email' => $this->organisation->email ?? '',
                'phone' => $this->organisation->phone ?? '',
                'website' => $this->organisation->website ?? '',
                'description' => $this->organisation->description ?? '',
                'organisation_type_id' => $this->organisation->organisation_type_id ?? 0,
                'status' => $this->organisation->status,
                'max_students' => $this->organisation->max_students,
                'address_line1' => $this->organisation->address_line1 ?? '',
                'address_line2' => $this->organisation->address_line2 ?? '',
                'city' => $this->organisation->city ?? '',
                'state_id' => $this->organisation->state_id ?? 0,
                'district_id' => $this->organisation->district_id ?? 0,
                'country' => $this->organisation->country ?? '',
                'postal_code' => $this->organisation->postal_code ?? '',
                'contact_person' => $this->organisation->contact_person ?? '',
                'contact_person_phone' => $this->organisation->contact_person_phone ?? '',
                'contact_person_email' => $this->organisation->contact_person_email ?? '',
                'subscription_start' => $this->organisation->subscription_start?->format('Y-m-d') ?? '',
                'subscription_end' => $this->organisation->subscription_end?->format('Y-m-d') ?? '',
            ]);

            $this->existingLogo = $this->organisation->logo;
            $this->existingBanner = $this->organisation->banner;

            // Load districts for selected state
            if ($this->state_id) {
                $this->districts = District::where('state_id', $this->state_id)->get();
            }
        }
    }

    public function updatedStateId(): void
    {
        $this->districts = District::where('state_id', $this->state_id)->get();
        $this->district_id = 0; // Reset district selection
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = collect($validated)
            ->except(['logo', 'banner'])
            ->toArray();

        $data['slug'] = Str::slug($this->name);

        // Handle Logo Upload
        if ($this->logo) {
            $data['logo'] = $this->logo->store('organisations/logos', 'public');
        }

        // Handle Banner Upload
        if ($this->banner) {
            $data['banner'] = $this->banner->store('organisations/banners', 'public');
        }

        if ($this->isEditing) {
            $this->organisation->update($data);
            $this->dispatch('notify', type: 'success', message: 'Organisation updated successfully!');
        } else {
            Organisation::create($data);
            $this->dispatch('notify', type: 'success', message: 'Organisation created successfully!');
            $this->redirect(route('admin.organisations.index'), navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.admin.organisation.organisation-form', [
            'organisationTypes' => $this->organisationTypes,
            'states' => $this->states,
            'districts' => $this->districts,
        ]);
    }
}
