<?php
// app/Livewire/Admin/Reports/StudentDetailedReport.php


namespace App\Livewire\Admin\Reports;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\StudentReportService;
use App\Models\User;

#[Layout('layouts.backend')]
class StudentDetailedReport extends Component
{
    public $studentId;
    public $reportData;

    public function mount($studentId)
    {
        $this->studentId = $studentId;
        $this->loadReport();
    }

    public function loadReport()
    {
        $user = User::whereHas('details', function($q) {
            $q->where('id', $this->studentId);
        })->firstOrFail();

        $reportService = app(StudentReportService::class);
        $this->reportData = $reportService->getDetailedReport($user->id);

        if (!$this->reportData) {
            abort(404, 'No assessment data found for this student.');
        }
    }

    public function render()
    {

        return view('livewire.admin.reports.student-detailed-report');
    }
}
