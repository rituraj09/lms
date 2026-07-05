<?php

namespace App\Livewire\Admin\Reports;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.backend')]
class ActivityLogs extends Component
{
    use WithPagination;

    // ── Filters ────────────────────────────────────────────────
    public string $search      = '';
    public string $userType    = '';
    public string $action      = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public int    $perPage     = 15;

    // ── Sorting ────────────────────────────────────────────────
    public string $sortField   = 'created_at';
    public string $sortDir     = 'desc';

    // ── Detail View ────────────────────────────────────────────
    public int    $view        = 0;   // 0 = list, 1 = detail
    public ?int   $selectedLog = null;

    // ─────────────────────────────────────────────────────────
    // MOUNT
    // ─────────────────────────────────────────────────────────
    public function mount(): void
    {
        if (! auth()->guard('admin')->user()->hasSystemPermission('system.activity.view')) {
            abort(403);
        }
    }

    // ─────────────────────────────────────────────────────────
    // RESET PAGE ON FILTER CHANGE
    // ─────────────────────────────────────────────────────────
    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedUserType(): void { $this->resetPage(); }
    public function updatedAction(): void   { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }
    public function updatedPerPage(): void  { $this->resetPage(); }

    // ─────────────────────────────────────────────────────────
    // SORT
    // ─────────────────────────────────────────────────────────
    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'desc';
        }
    }

    // ─────────────────────────────────────────────────────────
    // CLEAR FILTERS
    // ─────────────────────────────────────────────────────────
    public function clearFilters(): void
    {
        $this->reset(['search', 'userType', 'action', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────
    // VIEW DETAIL
    // ─────────────────────────────────────────────────────────
    public function viewDetail(int $logId): void
    {
        $this->selectedLog = $logId;
        $this->view        = 1;
    }

    public function backToList(): void
    {
        $this->selectedLog = null;
        $this->view        = 0;
    }

    // ─────────────────────────────────────────────────────────
    // GET SELECTED LOG WITH USER
    // ─────────────────────────────────────────────────────────
    public function getSelectedLogDetailProperty(): ?array
    {
        if (! $this->selectedLog) return null;

        $log = ActivityLog::find($this->selectedLog);

        if (! $log) return null;

        return $this->formatLogWithUser($log);
    }

    // ─────────────────────────────────────────────────────────
    // RESOLVE USER NAME FROM ADMIN OR USER MODEL
    // ─────────────────────────────────────────────────────────
    private function resolveUser(int $userId, string $userType): array
    {
        if ($userId === 0) {
            return [
                'name'   => 'System / Guest',
                'email'  => '—',
                'mobile' => '—',
                'avatar' => null,
            ];
        }

        if ($userType === 'admin') {
            $admin = Admin::find($userId);
            return [
                'name'   => $admin?->name   ?? 'Unknown Admin',
                'email'  => $admin?->email  ?? '—',
                'mobile' => $admin?->mobile ?? '—',
                'avatar' => $admin?->avatar ?? null,
            ];
        }

        $user = User::find($userId);
        return [
            'name'   => $user?->name   ?? 'Unknown User',
            'email'  => $user?->email  ?? '—',
            'mobile' => $user?->mobile ?? '—',
            'avatar' => $user?->avatar ?? null,
        ];
    }

    // ─────────────────────────────────────────────────────────
    // FORMAT LOG WITH USER
    // ─────────────────────────────────────────────────────────
    private function formatLogWithUser(ActivityLog $log): array
    {
        return [
            'id'          => $log->id,
            'user_id'     => $log->user_id,
            'user_type'   => $log->user_type,
            'action'      => $log->action,
            'model_type'  => $log->model_type,
            'model_id'    => $log->model_id,
            'description' => $log->description,
            'properties'  => $log->properties,
            'ip_address'  => $log->ip_address,
            'user_agent'  => $log->user_agent,
            'created_at'  => $log->created_at,
            'user'        => $this->resolveUser($log->user_id, $log->user_type),
        ];
    }

    // ─────────────────────────────────────────────────────────
    // UNIQUE ACTIONS FOR FILTER DROPDOWN
    // ─────────────────────────────────────────────────────────
    public function getUniqueActionsProperty(): array
    {
        return ActivityLog::distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────
    // QUERY WITH USER NAME
    // ─────────────────────────────────────────────────────────
    public function getLogsProperty()
    {
        $logs = ActivityLog::query()
            ->when($this->userType, fn($q) => $q->where('user_type', $this->userType))
            ->when($this->action,   fn($q) => $q->where('action', $this->action))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search,   fn($q) =>
            $q->where(function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('action',     'like', "%{$this->search}%")
                    ->orWhere('ip_address', 'like', "%{$this->search}%")
                    ->orWhere('user_id',    'like', "%{$this->search}%");
            })
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        // ✅ Attach user info to each log
        $logs->through(function ($log) {
            $log->user_info = $this->resolveUser($log->user_id, $log->user_type);
            return $log;
        });

        return $logs;
    }

    // ─────────────────────────────────────────────────────────
    // RENDER
    // ─────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.admin.reports.activity-logs', [
            'logs'              => $this->view === 0 ? $this->logs : collect(),
            'uniqueActions'     => $this->uniqueActions,
            'selectedLogDetail' => $this->view === 1 ? $this->selectedLogDetail : null,
        ]);
    }
}
