<?php
// app/Livewire/Admin/Student/StudentForm.php

namespace App\Livewire\Admin\Student;

use App\Models\User;
use App\Models\Master\UserDetail;
use App\Models\Master\State;
use App\Models\Master\District;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\PromotionService;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Layout('layouts.backend')]
class StudentForm extends Component
{
    use WithFileUploads;

    public $organisationId;
    public $studentId;
    public $isEditing = false;

    // User fields
    public $name;
    public $email;
    public $phone;
    public $avatar;
    public $existingAvatar;
    public $status = 'active';

    // User detail fields
    public $first_name;
    public $last_name;
    public $gender;
    public $date_of_birth;
    public $address_line1;
    public $address_line2;
    public $city;
    public $state_id;
    public $district_id;
    public $country = 'India';
    public $postal_code;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $bio;

    // Form helpers
    public $states = [];
    public $districts = [];
    public $generatedPassword;
    public $showPasswordModal = false;
    public $generatedStudentId;
    public $generatedEmail;
    public $canEnrollStudent = true;
    public $existingAvatarUrl = null;
    public $maxStudentMessage = '';
    public function mount($organisationId, $studentId = null)
    {
        try {
            $this->organisationId = Crypt::decrypt($organisationId);

            if ($studentId) {
                $this->studentId = Crypt::decrypt($studentId);
                $this->isEditing = true;
            }
        } catch (\Exception $e) {
            abort(404, 'Invalid parameters');
        }

        $admin = auth()->user();

        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($this->organisationId)) {
            abort(403, 'Unauthorized access to this organisation');
        }


        $this->loadStates();

        if ($this->isEditing) {
            $this->loadStudent();
        } else {
            // Check max student limit only for new enrollment
            $this->checkMaxStudentLimit();
        }
    }
// Add this new method
    public function checkMaxStudentLimit()
    {
        $organisation = \App\Models\Master\Organisation::find($this->organisationId);

        if ($organisation && $organisation->max_students > 0) {
            $currentStudentCount = \App\Models\User::where('organisation_id', $this->organisationId)
                ->whereIn('status', ['active', 'inactive', 'suspended'])
                ->count();

            if ($currentStudentCount >= $organisation->max_students) {
                $this->canEnrollStudent = false;
                $this->maxStudentMessage = "Maximum student enrollment limit reached ({$organisation->max_students} students). Please contact administrator to increase the limit.";
            }
        }
    }
    public function loadStates()
    {
        $this->states = State::orderBy('name')->get();
    }

    public function loadStudent()
    {
        $user = User::with('details')->find($this->studentId);

        if (!$user || $user->organisation_id !== $this->organisationId) {
            abort(404, 'Student not found');
        }

        $this->name   = $user->name;
        $this->email  = $user->email;
        $this->phone  = $user->phone;
        $this->status = $user->status;

        // ✅ Set BOTH the path AND the URL on load
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            $this->existingAvatar    = $user->avatar;
            $this->existingAvatarUrl = asset('storage/' . $user->avatar);
        } else {
            $this->existingAvatar    = null;
            $this->existingAvatarUrl = null;
        }

        if ($user->details) {
            $this->first_name              = $user->details->first_name;
            $this->last_name               = $user->details->last_name;
            $this->gender                  = $user->details->gender;
            $this->date_of_birth           = $user->details->date_of_birth?->format('Y-m-d');
            $this->address_line1           = $user->details->address_line1;
            $this->address_line2           = $user->details->address_line2;
            $this->city                    = $user->details->city;
            $this->state_id                = $user->details->state_id;
            $this->district_id             = $user->details->district_id;
            $this->country                 = $user->details->country;
            $this->postal_code             = $user->details->postal_code;
            $this->emergency_contact_name  = $user->details->emergency_contact_name;
            $this->emergency_contact_phone = $user->details->emergency_contact_phone;
            $this->bio                     = $user->details->bio;

            if ($this->state_id) {
                $this->districts = District::where('state_id', $this->state_id)
                    ->orderBy('name')
                    ->get();
            }
        }
    }

    public function updatedStateId($value)
    {
        $this->district_id = null;
        $this->districts = [];

        if ($value) {
            $this->districts = District::where('state_id', $value)
                ->orderBy('name')
                ->get();
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,suspended,pending',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'bio' => 'nullable|string|max:1000',
        ];

        if ($this->isEditing && $this->studentId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->studentId;
            $rules['phone'] = 'required|string|max:15|unique:users,phone,' . $this->studentId;
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Student name is required',
        'email.required' => 'Email address is required',
        'email.unique' => 'This email is already registered',
        'phone.required' => 'Phone number is required',
        'phone.unique' => 'This phone number is already registered',
        'first_name.required' => 'First name is required',
        'last_name.required' => 'Last name is required',
        'gender.required' => 'Please select gender',
        'date_of_birth.required' => 'Date of birth is required',
        'date_of_birth.before' => 'Date of birth must be in the past',
    ];

    public function save()
    {
        if (!$this->isEditing && !$this->canEnrollStudent) {
            $this->dispatch('notify', [
                'message' => 'Cannot enroll student. Maximum limit reached.',
                'type' => 'error'
            ]);
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->isEditing) {
                $this->updateStudent();
            } else {
                $this->createStudent();
            }

            DB::commit();

            if ($this->isEditing) {
                session()->flash('success', 'Student updated successfully!');
                return redirect()->route('admin.org.students', [
                    'organisationId' => Crypt::encrypt($this->organisationId)
                ]);
            } else {
                $this->showPasswordModal = true;
            }

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Failed to save student: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function createStudent()
    {
        $this->generatedPassword = Str::password(12);

        $avatarPath = null;
        if ($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
        }

        // ✅ NO nested transaction - save() already handles the outer transaction
        $user = User::create([
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'password'        => Hash::make($this->generatedPassword),
            'status'          => $this->status,
            'avatar'          => $avatarPath,
            'organisation_id' => $this->organisationId,
        ]);

        $userDetail = UserDetail::create([
            'user_id'                 => $user->id,
            'first_name'              => $this->first_name,
            'last_name'               => $this->last_name,
            'gender'                  => $this->gender,
            'date_of_birth'           => $this->date_of_birth,
            'address_line1'           => $this->address_line1,
            'address_line2'           => $this->address_line2,
            'city'                    => $this->city,
            'state_id'                => $this->state_id,
            'district_id'             => $this->district_id,
            'country'                 => $this->country,
            'postal_code'             => $this->postal_code,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'bio'                     => $this->bio,
        ]);
        ActivityLogger::log(
            userId:   auth('admin')->id(),
            userType: 'admin',
            action:   'create',
            extra: [
                'model_type'  => 'User',
                'model_id'    =>  $user->id,
                'description' => "Created Student: {$user->name}",
                'properties'  => [
                    'student_id' => $user->details->student_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ]
        );
        // Initialize promotion records (iq, eq, lq at starting level)
        $promotionService = app(PromotionService::class);
        $initialized = $promotionService->initializeUserPromotion($user->id);


        if (!$initialized) {
            // ✅ Let this bubble up to save()'s catch block for proper rollback
            throw new \RuntimeException('Failed to initialize user promotion details.');
        }

        $this->generatedStudentId = $userDetail->student_id;
        $this->generatedEmail = $user->email;
        $this->studentId= $userDetail->student_id;
    }

    protected function updateStudent()
    {
        $user = User::find($this->studentId);

        if (!$user || $user->organisation_id !== $this->organisationId) {
            throw new \Exception('Student not found');
        }

        // ✅ Handle avatar upload properly
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $this->avatar->store('avatars', 'public');
        } else {
            // ✅ Keep existing avatar path — don't overwrite with null
            $avatarPath = $user->avatar;
        }

        $user->update([
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'status'    => $this->status,
            'avatar'    => $avatarPath, // ✅ Now correctly defined
        ]);

        $user->details()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name'              => $this->first_name,
                'last_name'               => $this->last_name,
                'gender'                  => $this->gender,
                'date_of_birth'           => $this->date_of_birth,
                'address_line1'           => $this->address_line1,
                'address_line2'           => $this->address_line2,
                'city'                    => $this->city,
                'state_id'                => $this->state_id,
                'district_id'             => $this->district_id,
                'country'                 => $this->country,
                'postal_code'             => $this->postal_code,
                'emergency_contact_name'  => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'bio'                     => $this->bio,
            ]
        );
        ActivityLogger::log(
            userId:   auth('admin')->id(),
            userType: 'admin',
            action:   'update',
            extra: [
                'model_type'  => 'User',
                'model_id'    =>  $user->id,
                'description' => "Updated Student: {$user->name}",
                'properties'  => [
                    'student_id' => $user->details->student_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ]
        );
    }
// Add this computed property to get avatar URL
    public function getAvatarPreviewUrl(): ?string
    {
        if ($this->avatar) {
            return $this->avatar->temporaryUrl();
        }

        if ($this->existingAvatar) {
            return \Storage::disk('public')->url($this->existingAvatar);
        }

        return null;
    }

    // app/Livewire/Admin/Student/StudentForm.php

    public function removeAvatar()
    {
        if ($this->existingAvatar) {
            if (\Storage::disk('public')->exists($this->existingAvatar)) {
                \Storage::disk('public')->delete($this->existingAvatar);
            }

            if ($this->isEditing && $this->studentId) {
                User::where('id', $this->studentId)->update(['avatar' => null]);
            }
        }

        $this->existingAvatar    = null;
        $this->existingAvatarUrl = null; // ✅ Clear URL too
        $this->avatar            = null;
    }


    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
        return redirect()->route('admin.org.students', [
            'organisationId' => Crypt::encrypt($this->organisationId)
        ]);
    }

    public function render()
    {
        return view('livewire.admin.student.student-form');
    }
}
