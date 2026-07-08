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
use App\Services\ActivityLogger;

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

    public function mount(?string $id = null): void
    {

        $this->roles = Role::where('guard_name', 'admin')->get();


        if ($id) {
            $id = decrypt($id);
            $this->organisations = Organisation::active()->get();
            $this->admin = Admin::with(['details', 'organisations'])->findOrFail($id);
            $this->isEditing = true;

            $this->fill([
                'name'             => $this->admin->name,
                'email'            => $this->admin->email,
                'mobile'           => $this->admin->mobile,
                'status'           => $this->admin->status,
                'first_name'       => $this->admin->details?->first_name ?? '',
                'last_name'        => $this->admin->details?->last_name ?? '',
                'designation'      => $this->admin->details?->designation ?? '',
                'role_id'          => $this->admin->roles->first()?->id ?? 0,
                'organisation_ids' => $this->admin->organisations->pluck('id')->toArray(),
            ]);
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = collect($validated)
            ->except([
                'password',
                'password_confirmation',
                'organisation_ids',
                'first_name',
                'last_name',
                'designation',
                'role_id',
            ])
            ->toArray();

        if ($this->isEditing) {

            // Capture old values before update for comparison
            $oldData = [
                'name'         => $this->admin->name,
                'email'        => $this->admin->email,
                'mobile'       => $this->admin->mobile,
                'status'       => $this->admin->status,
                'first_name'   => $this->admin->details?->first_name,
                'last_name'    => $this->admin->details?->last_name,
                'designation'  => $this->admin->details?->designation,
                'role'         => $this->admin->roles->first()?->name,
                'organisations'=> $this->admin->organisations->pluck('id')->toArray(),
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->admin->update($data);

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
                    'first_name'  => $this->first_name,
                    'last_name'   => $this->last_name,
                    'designation' => $this->designation,
                ]
            );

            // Capture new values after update
            $newData = [
                'name'          => $this->name,
                'email'         => $this->email,
                'mobile'        => $this->mobile,
                'status'        => $this->status,
                'first_name'    => $this->first_name,
                'last_name'     => $this->last_name,
                'designation'   => $this->designation,
                'role'          => Role::find($this->role_id)?->name,
                'organisations' => $this->organisation_ids,
            ];

            // Log update activity
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   'update',
                extra: [
                    'model_type'  => 'Admin',
                    'model_id'    => $this->admin->id,
                    'description' => "Updated admin: {$this->admin->name}",
                    'properties'  => [
                        'old' => $oldData,
                        'new' => $newData,
                    ],
                ]
            );

        } else {

            $data['password'] = Hash::make($this->password);
            $this->admin = Admin::create($data);

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
                    'first_name'  => $this->first_name,
                    'last_name'   => $this->last_name,
                    'designation' => $this->designation,
                ]
            );

            // Log create activity
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   'create',
                extra: [
                    'model_type'  => 'Admin',
                    'model_id'    => $this->admin->id,
                    'description' => "Created admin: {$this->admin->name}",
                    'properties'  => [
                        'name'          => $this->name,
                        'email'         => $this->email,
                        'mobile'        => $this->mobile,
                        'organisations' => $this->organisation_ids,
                    ],
                ]
            );
        }

        $this->dispatch('notify', type: 'success',
            message: $this->isEditing ? 'Admin updated successfully!' : 'Admin created successfully!');

        $this->redirect(route('admin.admins.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.admin-management.admin-form', [
            'roles'         => $this->roles,
            'organisations' => $this->organisations,
        ]);
    }
}
