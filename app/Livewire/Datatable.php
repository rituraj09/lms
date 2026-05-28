<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Database\Eloquent\Builder;

class Datatable extends Component
{
    use WithPagination, WithoutUrlPagination;

    // ── Props ─────────────────────────────────────────────────
    public string $model = '';
    public array $columns = [];
    public array $actions = [];
    public array $filters = [];
    public array $bulkActions = [];
    public bool $exportable = true;
    public bool $newEntry= false;
    public string $title = 'Data Table';
    public string $emptyMessage = 'No records found.';

    // ── URL-bound state ───────────────────────────────────────
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = '';

    #[Url]
    public string $sortDir = 'asc';

    #[Url]
    public int $perPage = 10;

    // ── Local state ───────────────────────────────────────────
    public array $activeFilters = [];
    public array $selectedRows = [];
    public bool $selectAll = false;

    public array $perPageOptions = [10, 25, 50, 100];

    // ─────────────────────────────────────────────────────────
    // Lifecycle
    // ─────────────────────────────────────────────────────────

    public function mount(
        string $model,
        array  $columns,
        array  $actions = [],
        array  $filters = [],
        array  $bulkActions = [],
        bool   $exportable = true,
        string $title = 'Data Table',
        string $emptyMessage = 'No records found.',
    ): void
    {
        $this->model = $model;
        $this->columns = $columns;
        $this->actions = $actions;
        $this->filters = $filters;
        $this->bulkActions = $bulkActions;
        $this->exportable = $exportable;
        $this->title = $title;
        $this->emptyMessage = $emptyMessage;

        if (empty($this->sortBy)) {
            $first = collect($columns)->firstWhere('sortable', true);
            $this->sortBy = $first['key'] ?? 'id';
        }
    }

    // ─────────────────────────────────────────────────────────
    // Computed
    // ─────────────────────────────────────────────────────────

    #[Computed]
    public function rows()
    {
        /** @var Builder $query */
        $query = ($this->model)::query();

        // Global search
        if ($this->search !== '') {
            $searchable = collect($this->columns)
                ->where('searchable', true)
                ->pluck('key');

            $query->where(function (Builder $q) use ($searchable) {
                foreach ($searchable as $col) {
                    $q->orWhere($col, 'like', '%' . $this->search . '%');
                }
            });
        }

        // Column filters
        foreach ($this->activeFilters as $key => $value) {
            if ($value !== '' && $value !== null) {
                $query->where($key, $value);
            }
        }

        // Sort
        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDir);
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function searchableColumns(): array
    {
        return collect($this->columns)
            ->where('searchable', true)
            ->pluck('label')
            ->toArray();
    }

    #[Computed]
    public function visibleColumns(): array
    {
        return collect($this->columns)
            ->where('hidden', '!=', true)
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────
    // Sorting
    // ─────────────────────────────────────────────────────────

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────
    // Search / filter watchers
    // ─────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $key, mixed $value): void
    {
        $this->activeFilters[$key] = $value;
        $this->resetPage();
    }

    public function clearFilter(string $key): void
    {
        unset($this->activeFilters[$key]);
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->activeFilters = [];
        $this->search = '';
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────
    // Row selection
    // ─────────────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedRows = $value
            ? $this->rows->pluck('id')->map(fn($id) => (string)$id)->toArray()
            : [];
    }

    public function updatedSelectedRows(): void
    {
        $this->selectAll = count($this->selectedRows) === $this->rows->count();
    }

    // ─────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────

    public function dispatchAction(string $event, int|string $rowId): void
    {
        $this->dispatch($event, id: $rowId);
    }

    public function dispatchBulkAction(string $event): void
    {
        $this->dispatch($event, ids: $this->selectedRows);
        $this->selectedRows = [];
        $this->selectAll = false;
    }
    public function dispatchEntry() : void
    {
        $this->dispatch('new-entry');
    }
    public function toggleSwitch(string $event, int|string $rowId, bool $currentValue): void
    {
        // Find the column definition that owns this event
        $column = collect($this->columns)->firstWhere('event', $event);

        if (! $column) {
            return;
        }

        $field    = $column['key'];
        $newValue = ! $currentValue;

        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = ($this->model)::findOrFail($rowId);
        $record->update([$field => $newValue]);

        // Notify parent components
        $this->dispatch($event, id: $rowId, value: $newValue, field: $field);

        // Inline toast
        $onLabel  = $column['onLabel']  ?? 'Active';
        $offLabel = $column['offLabel'] ?? 'Inactive';
        $label    = $newValue ? $onLabel : $offLabel;

        $this->dispatch('row-action-success',
            message: ($column['toastMessage'] ?? 'Status updated to') . ' ' . $label . '.',
        );
    }
    // ─────────────────────────────────────────────────────────
    // Export (CSV)
    // ─────────────────────────────────────────────────────────

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $exportable = collect($this->columns)->where('type', '!=', 'actions')->toArray();
        $headers = collect($exportable)->pluck('label')->toArray();
        $keys = collect($exportable)->pluck('key')->toArray();

        /** @var Builder $query */
        $query = ($this->model)::query();

        if ($this->search !== '') {
            $searchable = collect($this->columns)->where('searchable', true)->pluck('key');
            $query->where(function (Builder $q) use ($searchable) {
                foreach ($searchable as $col) {
                    $q->orWhere($col, 'like', '%' . $this->search . '%');
                }
            });
        }

        foreach ($this->activeFilters as $key => $value) {
            if ($value !== '' && $value !== null) {
                $query->where($key, $value);
            }
        }

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDir);
        }

        $records = $query->get();

        return response()->streamDownload(function () use ($records, $headers, $keys) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($records as $record) {
                fputcsv($handle, array_map(fn($k) => $record->{$k} ?? '', $keys));
            }
            fclose($handle);
        }, 'export-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
    public function render()
    {
        return view('livewire.datatable');
    }

    #[On('refresh-table')]
    public function refreshTable(): void
    {

    }
}
