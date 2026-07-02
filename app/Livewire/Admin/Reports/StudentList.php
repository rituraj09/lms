<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Master\Organisation;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class StudentList extends Component
{
    public int $view = 0; // 0 = datatable, 1 = user details
    public ?User $selectedUser = null;

    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }

    #[On('edit')]
    public function viewUserDetails(int $id): void
    {
        $this->selectedUser = User::with(['details', 'organisation'])->findOrFail($id);
        $this->view = 1;
    }
    #[On('report_card')]
    public function viewUserReport(int $id): void
    {
        $this->redirect(
            route('admin.reports.detailed-report', ['studentId' => $id]),   navigate: false);
    }
    public function backToList(): void
    {
        $this->view = 0;
        $this->selectedUser = null;
    }

    public function render()
    {
        return view('livewire.admin.reports.student-list');
    }
}
