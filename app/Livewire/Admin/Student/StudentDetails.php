<?php
// app/Livewire/Admin/Student/StudentDetails.php

namespace App\Livewire\Admin\Student;

use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
#[Layout('layouts.backend')]
class StudentDetails extends Component
{
    public $organisationId;
    public $studentId;
    public $student;

    // Reset Password
    public $showResetPasswordModal = false;
    public $newPassword;

    public function mount($organisationId, $studentId)
    {
        try {
            $this->organisationId = Crypt::decrypt($organisationId);
            $this->studentId = Crypt::decrypt($studentId);
        } catch (\Exception $e) {
            abort(404, 'Invalid parameters');
        }

        $admin = auth()->user();

        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($this->organisationId)) {
            abort(403, 'Unauthorized access to this organisation');
        }

        $this->loadStudent();
    }

    public function loadStudent()
    {
        $this->student = User::with([
            'details.state',
            'details.district',
            'organisation'
        ])->find($this->studentId);

        if (!$this->student || $this->student->organisation_id !== $this->organisationId) {
            abort(404, 'Student not found');
        }
    }

    public function resetPassword()
    {
        $admin = auth()->user();

        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($this->organisationId)) {
            $this->dispatch('notify', [
                'message' => 'You do not have permission to reset password',
                'type' => 'error'
            ]);
            return;
        }

        // Generate new password
        $this->newPassword = Str::password(12);

        // Update password
        $this->student->update([
            'password' => Hash::make($this->newPassword)
        ]);
        ActivityLogger::log(
            userId:   auth('admin')->id(),
            userType: 'admin',
            action:   'update',
            extra: [
                'model_type'  => 'User',
                'model_id'    =>  $this->student->id,
                'description' => "Password Changed Student: {$this->student->name}",

            ]
        );
        // Show modal with new password
        $this->showResetPasswordModal = true;

        // Optionally send email to student
        // Mail::to($this->student->email)->send(new PasswordResetMail($this->newPassword));
    }

    public function closeResetPasswordModal()
    {
        $this->showResetPasswordModal = false;
        $this->newPassword = null;
    }

    public function render()
    {
        return view('livewire.admin.student.student-details');
    }
}
