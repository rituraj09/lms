<?php
// app/Livewire/Admin/AdminManagement/AdminForm.php


namespace App\Livewire\Admin\AdminManagement;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Master\AdminDetail;
use App\Models\Master\Organisation;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class AdminForm extends Component
{
    public ?Admin $admin = null;
    public bool $isEditing = false;

    // Form Fields
    public string $name = '';
    public string $email = '';
    public string $mobile = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $status = 'active';

    // Details
    public string $first_name = '';
    public string $last_name = '';
    public string $designation = '';

    // Role & Organisations
    public int $role_id = 0;
    public array $organisation_ids = [];

    public $roles = [];
    public $organisations = [];

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => $this->isEditing
                ? "required|email|unique:admins,email,{$this->admin->id}"
                : 'required|email|unique:admins,email',
            'mobile' => $this->isEditing
                ? "required|digits:10|unique:admins,mobile,{$this->admin->id}"
                : 'required|digits:10|unique:admins,mobile',
            'status' => 'required|in:active,inactive,suspended',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'role_id' => 'required|integer|exists:roles,id',
            'organisation_ids' => 'array',
            'organisation_ids.*' => 'integer|exists:organisations,id',
        ];

        if (!$this->isEditing) {
            $rules['password'] = [
                'required',
                'confirmed',
                'regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,16}$/',
            ];
        } else {
            $rules['password'] = 'nullable|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'password.regex' => 'Password must be 8-16 characters with uppercase, lowercase, number & special character.',
    ];

    public function mount(?int $id = null): void
    {
        $this->roles = Role::where('guard_name', 'admin')->get();
        $this->organisations = Organisation::active()->get();

        if ($id) {
            $this->admin = Admin::with(['details', 'organisations'])->findOrFail($id);
            $this->isEditing = true;

            $this->fill([
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'mobile' => $this->admin->mobile,
                'status' => $this->admin->status,
                'first_name' => $this->admin->details?->first_name ?? '',
                'last_name' => $this->admin->details?->last_name ?? '',
                'designation' => $this->admin->details?->designation ?? '',
                'role_id' => $this->admin->roles->first()?->id ?? 0,
                'organisation_ids' => $this->admin->organisations->pluck('id')->toArray(),
            ]);
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = collect($validated)
            ->except(['password', 'password_confirmation', 'organisation_ids', 'first_name', 'last_name', 'designation', 'role_id'])
            ->toArray();

        if ($this->isEditing) {
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $this->admin->update($data);
        } else {
            $data['password'] = Hash::make($this->password);
            $this->admin = Admin::create($data);
        }

        // Update role
        if ($this->role_id) {
            $role = Role::findOrFail($this->role_id);
            $this->admin->syncRoles([$role->name]);
        }

        // Update organisations
        $this->admin->organisations()->sync($this->organisation_ids);

        // Update details
        AdminDetail::updateOrCreate(
            ['admin_id' => $this->admin->id],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'designation' => $this->designation,
            ]
        );

        $this->dispatch('notify', type: 'success',
            message: $this->isEditing ? 'Admin updated successfully!' : 'Admin created successfully!');

        $this->redirect(route('admin.admins.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.admin-management.admin-form', [
            'roles' => $this->roles,
            'organisations' => $this->organisations,
        ]);
    }
}
