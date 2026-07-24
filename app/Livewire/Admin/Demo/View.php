<?php

namespace App\Livewire\Admin\Demo;

use App\Models\DemoRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class View extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public ?DemoRequest $selectedRequest = null;

    protected $queryString = ['search', 'statusFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewDetails($id)
    {
        $this->selectedRequest = DemoRequest::findOrFail($id);
        $this->dispatch('show-modal');
    }

    public function updateStatus($id, $status)
    {
        $request = DemoRequest::findOrFail($id);
        $request->update(['status' => $status]);

        if ($this->selectedRequest && $this->selectedRequest->id === $id) {
            $this->selectedRequest->refresh();
        }

        $this->dispatch('demo-request-updated'); // 🔔 notify badge
        $this->dispatch('notify', message: 'Status updated to "' . ucfirst($status) . '" successfully.');
    }

    public function deleteRequest($id)
    {
        DemoRequest::findOrFail($id)->delete();

        $this->dispatch('demo-request-updated'); // 🔔 notify badge
        $this->dispatch('notify', message: 'Demo request deleted successfully.');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $requests = DemoRequest::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('institution', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.demo.view', [
            'requests' => $requests,
        ]);
    }
}
