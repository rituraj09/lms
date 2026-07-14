<?php
// app/Livewire/Admin/Student/ManageStudents.php

namespace App\Livewire\Admin\Student;

use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class ManageStudents extends Component
{
    use WithPagination;

    public $organisationId;
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $canEnrollStudent = true;
    public $maxStudentCount = 0;
    public $currentStudentCount = 0;
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount($organisationId)
    {
        try {
            $this->organisationId = Crypt::decrypt($organisationId);
        } catch (\Exception $e) {
            abort(404, 'Invalid organisation');
        }

        $admin = auth()->user();

        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($this->organisationId)) {
            abort(403, 'Unauthorized access to this organisation');
        }
        $this->checkEnrollmentLimit();
    }
    public function checkEnrollmentLimit()
    {
        $organisation = \App\Models\Master\Organisation::find($this->organisationId);

        if ($organisation) {
            $this->maxStudentCount = $organisation->max_students;
            $this->currentStudentCount = User::where('organisation_id', $this->organisationId)
                ->whereIn('status', ['active', 'inactive', 'suspended'])
                ->count();

            if ($this->maxStudentCount > 0 && $this->currentStudentCount >= $this->maxStudentCount) {
                $this->canEnrollStudent = false;
            }
        }
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function deleteStudent($userId)
    {
        $admin = auth()->user();

        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($this->organisationId)) {
            $this->dispatch('notify', [
                'message' => 'You do not have permission to delete students',
                'type' => 'error'
            ]);
            return;
        }

        $user = User::find($userId);

        if ($user && $user->organisation_id === $this->organisationId) {
            // Delete avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   'delete',
                extra: [
                    'model_type'  => 'User',
                    'model_id'    =>  $user->id,
                    'description' => "Deleted Student: {$user->name}",

                ]
            );
            $this->dispatch('notify', [
                'message' => 'Student deleted successfully',
                'type' => 'success'
            ]);
        }
    }

   public function render()
    {
        $students = User::with([
            'details',
            'organisation',
            'userPromotions' => function($query) {
                $query->current(); // Use the scope from UserPromotionDetail model
            },
            'userPromotions.promotionDetail.currentPromotion',
        ])
        ->where('organisation_id', $this->organisationId)
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details', function ($detailQuery) {
                        $detailQuery
                            ->where('student_id', 'like', '%' . $this->search . '%')
                            ->orWhere('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            });
        })
        ->when($this->statusFilter, function ($query) {
            $query->where('status', $this->statusFilter);
        })
        ->latest()
        ->paginate($this->perPage);

        // Transform the collection to add promotionMap
        $students->through(function ($student) {
            // Build promotion map for IQ, EQ, LQ
            $student->promotionMap = [
                'iq' => $this->getPromotionName($student, 'iq'),
                'eq' => $this->getPromotionName($student, 'eq'),
                'lq' => $this->getPromotionName($student, 'lq'),
            ];

            return $student;
        });

        return view('livewire.admin.student.manage-students', [
            'students' => $students,
        ]);
    }

    /**
     * Get the promotion name for a specific assessment type
     */
    private function getPromotionName($student, string $type): ?string
    {
        // userPromotions already filtered by current() scope
        $userPromotion = collect($student->userPromotions ?? [])
            ->where('assessment_type', $type)
            ->first();

        if (!$userPromotion) {
            return null;
        }

        return $userPromotion->promotionDetail?->currentPromotion?->name;
    }
}
