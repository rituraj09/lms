<div
    x-data="{
        showFilters: false,
        toast: null,
        showToast(message, type = 'success') {
            this.toast = { message, type };
            setTimeout(() => this.toast = null, 3500);
        }
    }"
    @row-action-success.window="showToast($event.detail.message, $event.detail.type ?? 'success')"
    class="card shadow-sm border-0 dt-card"
>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TOOLBAR                                                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="card-header border-bottom py-3 px-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0 text-md-start text-center">{{ $title }}</h5>
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-semibold"></h5>
                @if(count($selectedRows) > 0)
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                        {{ count($selectedRows) }} selected
                    </span>
                @endif

            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">

                {{-- Search --}}
                <div class="input-group input-group-sm">
                        <span class="input-group-text border-end-0 text-muted my-2">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd"
                                                                                                      d="M9 3a6 6 0 100 12A6 6 0 009 3zM1 9a8 8 0 1114.32 4.906l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387A8 8 0 011 9z"
                                                                                                      clip-rule="evenodd"/></svg>
                        </span>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        class="form-control form-control-sm border-start-0 ps-0 my-2"
                        placeholder="Search {{ implode(', ', $this->searchableColumns) }}…"
                    />

                    <div class="btn-group my-2 align-content-center">
                        @if($exportable)
                            <button wire:click="export('csv')"
                                    class="btn btn-success btn-fab btn-sm demo waves-effect waves-light ms-1">
                                <span class="icon-base ri ri-file-excel-2-fill"></span>
                                Export
                            </button>

                        @endif
                        @if($newEntry)
                            <button class="btn btn-primary btn-fab btn-sm demo waves-effect waves-light ms-1" wire:click="dispatchEntry">
                                    <span class="icon-base ri ri-add-line">
                                    </span> New {{Str::singular($title)}}
                            </button>
                        @endif
                    </div>
                </div>



                {{-- Filter toggle --}}
                @if(count($filters) > 0)
                    <button
                        @click="showFilters = !showFilters"
                        :class="showFilters ? 'btn-primary' : 'btn-outline-secondary'"
                        class="btn btn-sm d-inline-flex align-items-center gap-1"
                    >
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.553.894l-4 2A1 1 0 016 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Filters
                        @if(count($activeFilters) > 0)
                            <span class="badge text-primary"
                                  style="font-size:10px">{{ count($activeFilters) }}</span>
                        @endif
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ACTIVE FILTER PILLS                                         --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(count($activeFilters) > 0 || $search)
        <div class="d-flex flex-wrap align-items-center gap-2 px-4 py-2 bg-primary-subtle border-bottom">
            @if($search)
                <span
                    class="badge rounded-pill text-dark border border-primary-subtle fw-normal fs-xs px-3 py-2">
                    Search: "{{ $search }}"
                    <button wire:click="$set('search', '')" class="btn-close ms-1" style="font-size:9px"></button>
                </span>
            @endif
            @foreach($activeFilters as $key => $value)
                @if($value !== '')
                    <span
                        class="badge rounded-pill text-dark border border-primary-subtle fw-normal px-3 py-2">
                        {{ collect($filters)->firstWhere('key', $key)['label'] ?? $key }}: {{ $value }}
                        <button wire:click="clearFilter('{{ $key }}')" class="btn-close ms-1"
                                style="font-size:9px"></button>
                    </span>
                @endif
            @endforeach
            <button wire:click="clearAllFilters"
                    class="btn btn-link btn-sm text-primary p-0 fw-semibold text-decoration-none">
                Clear all
            </button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- FILTER PANEL                                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(count($filters) > 0)
        <div x-show="showFilters" x-collapse class="border-bottom bg-light px-4 py-3">
            <div class="row g-3">
                @foreach($filters as $filter)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-semibold text-uppercase text-muted mb-1"
                               style="font-size:11px;letter-spacing:.5px">
                            {{ $filter['label'] }}
                        </label>

                        @if(isset($filter['type']) && $filter['type'] === 'date-range')
                            <div class="d-flex align-items-center gap-1">
                                <input type="date"
                                       wire:change="setFilter('{{ $filter['key'] }}_from', $event.target.value)"
                                       class="form-control form-control-sm"/>
                                <span class="text-muted">–</span>
                                <input type="date"
                                       wire:change="setFilter('{{ $filter['key'] }}_to', $event.target.value)"
                                       class="form-control form-control-sm"/>
                            </div>
                        @elseif(isset($filter['options']))
                            <select wire:change="setFilter('{{ $filter['key'] }}', $event.target.value)"
                                    class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach($filter['options'] as $option)
                                    @if(is_array($option))
                                        <option
                                            value="{{ $option['value'] }}" {{ ($activeFilters[$filter['key']] ?? '') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                    @else
                                        <option
                                            value="{{ $option }}" {{ ($activeFilters[$filter['key']] ?? '') == $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="activeFilters.{{ $filter['key'] }}"
                                placeholder="Filter…"
                                class="form-control form-control-sm"
                            />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- BULK ACTION BAR                                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(count($selectedRows) > 0 && isset($bulkActions) && count($bulkActions) > 0)
        <div class="d-flex align-items-center gap-3 px-4 py-2 bg-warning-subtle border-bottom">
            <span class="small fw-semibold text-warning-emphasis">{{ count($selectedRows) }} row(s) selected</span>
            @foreach($bulkActions as $action)
                <button wire:click="dispatchBulkAction('{{ $action['event'] }}')"
                        class="btn btn-sm {{ $action['class'] ?? 'btn-outline-warning' }}">
                    {{ $action['label'] }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TABLE                                                        --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="table-responsive position-relative">
        <table class="table table-hover align-middle mb-0 dt-table table-sm fs-xsmall fw-semibold">
            <thead>
            <tr>
                @if(count($bulkActions) > 0)
                    <th style="width:44px" class="px-4">
                        <input type="checkbox" wire:model.live="selectAll" class="form-check-input"/>
                    </th>
                @endif

                @foreach($this->visibleColumns as $column)
                    <th
                        class="dt-th {{ ($column['sortable'] ?? false) ? 'dt-sortable' : '' }} {{ $sortBy === $column['key'] ? 'text-primary' : '' }}"
                        {{ ($column['sortable'] ?? false) ? 'wire:click=sort("'.$column['key'].'")' : '' }}
                        style="{{ isset($column['width']) ? 'width:'.$column['width'].';' : '' }}"
                    >
                            <span class="d-inline-flex align-items-center gap-1 user-select-none">
                                {{ $column['label'] }}
                                @if($column['sortable'] ?? false)
                                    @if($sortBy === $column['key'] && $sortDir === 'asc')
                                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"
                                             class="text-primary flex-shrink-0"><path fill-rule="evenodd"
                                                                                      d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"
                                                                                      clip-rule="evenodd"/></svg>
                                    @elseif($sortBy === $column['key'] && $sortDir === 'desc')
                                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"
                                             class="text-primary flex-shrink-0"><path fill-rule="evenodd"
                                                                                      d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                                                                      clip-rule="evenodd"/></svg>
                                    @else
                                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"
                                             class="opacity-25 flex-shrink-0"><path
                                                d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/></svg>
                                    @endif
                                @endif
                            </span>
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @forelse($this->rows as $row)
                <tr class="{{ in_array((string)$row->id, $selectedRows) ? 'table-primary' : '' }}">

                    @if(count($bulkActions) > 0)
                        <td class="px-4">
                            <input type="checkbox" wire:model.live="selectedRows" value="{{ $row->id }}"
                                   class="form-check-input"/>
                        </td>
                    @endif

                    @foreach($this->visibleColumns as $column)
                        <td>
                            {{-- Actions --}}
                            @if(($column['type'] ?? '') === 'actions')
                                <div class="d-flex gap-1">
                                    @foreach($actions as $action)
                                        @if($action['confirm'] ?? false)
                                            <button
                                                onclick="
                                                    if (confirm(@js($action['confirmText'] ?? 'Are you sure?'))) {
                                                        Livewire.find(this.closest('[wire\\:id]').getAttribute('wire:id'))
                                                            .call(
                                                                'dispatchAction',
                                                                @js($action['event']),
                                                                {{ $row->id }}
                                                            );
                                                    }
                                                    return false;
                                                "
                                                class="btn rounded-pill btn-icon btn-sm {{ $action['class'] ?? 'waves-effect waves-light' }}"
                                                title="{{ $action['label'] }}"
                                            ><span class="{{ $action['icon'] }}"></span></button>
                                        @else
                                            <button
                                                wire:click="dispatchAction('{{ $action['event'] }}', {{ $row->id }})"
                                                class="btn rounded-pill btn-icon btn-sm {{ $action['class'] ?? 'waves-effect waves-light' }}"
                                                title="{{ $action['label'] }}"
                                            ><span class="{{ $action['icon'] }}"></span></button>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Badge --}}
                            @elseif(($column['type'] ?? '') === 'badge')
                                @php $val = data_get($row, $column['key'], ''); $badgeClass = $column['badges'][$val] ?? 'bg-secondary'; @endphp
                                <span class="badge rounded-pill {{ $badgeClass }}">{{ $val }}</span>

                                {{-- Boolean --}}
                            @elseif(($column['type'] ?? '') === 'boolean')
                                @if(data_get($row, $column['key']))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor"><path
                                                    fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"/></svg>
                                            Yes
                                        </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor"><path
                                                    fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"/></svg>
                                            No
                                        </span>
                                @endif
                                {{-- Switch --}}
                            @elseif(($column['type'] ?? '') === 'switch')
                                @php
                                    $isOn        = (bool) data_get($row, $column['key']);
                                    $event       = $column['event']    ?? 'dt-toggle';
                                    $onLabel     = $column['onLabel']  ?? 'Active';
                                    $offLabel    = $column['offLabel'] ?? 'Inactive';
                                    $onColor     = $column['onColor']  ?? 'success';
                                    $offColor    = $column['offColor'] ?? 'secondary';
                                    $confirm     = $column['confirm']  ?? false;
                                    $confirmText = $column['confirmText'] ?? ($isOn ? 'Deactivate this record?' : 'Activate this record?');
                                    $switchId    = 'dt-switch-' . $column['key'] . '-' . $row->id;
                                @endphp
                                <div
                                    class="d-flex align-items-center gap-2 dt-switch-wrap"
                                    x-data="{ pending: false }"
                                >
                                    <div class="form-check form-switch mb-0">
                                        <input
                                            class="form-check-input dt-switch dt-switch--{{ $onColor }}"
                                            type="checkbox"
                                            role="switch"
                                            id="{{ $switchId }}"
                                            {{ $isOn ? 'checked' : '' }}
                                            :disabled="pending"
                                            @if($confirm)
                                                @change="
                                                        $event.preventDefault();
                                                        $event.target.checked = {{ $isOn ? 'true' : 'false' }};
                                                        if (confirm('{{ addslashes($confirmText) }}')) {
                                                            pending = true;
                                                            $wire.toggleSwitch('{{ $event }}', {{ $row->id }}, {{ $isOn ? 'true' : 'false' }})
                                                                 .then(() => pending = false);
                                                        }
                                                    "
                                            @else
                                                @change="
                                                        pending = true;
                                                        $wire.toggleSwitch('{{ $event }}', {{ $row->id }}, {{ $isOn ? 'true' : 'false' }})
                                                             .then(() => pending = false);
                                                    "
                                            @endif
                                            style="cursor: pointer;"
                                        />
                                    </div>
                                    <label
                                        for="{{ $switchId }}"
                                        class="mb-0 small fw-medium text-{{ $isOn ? $onColor : $offColor }}"
                                        style="cursor:pointer; min-width: 54px;"
                                        x-show="!pending"
                                    >
                                        {{ $isOn ? $onLabel : $offLabel }}
                                    </label>
                                    <span x-show="pending" class="spinner-border spinner-border-sm text-{{ $isOn ? $onColor : 'secondary' }}" role="status" style="width:14px;height:14px;border-width:2px">
                                            <span class="visually-hidden">Saving…</span>
                                        </span>
                                </div>
                                {{-- Date --}}
                            @elseif(($column['type'] ?? '') === 'date')
                                <span
                                    class="text-muted small">{{ $row->{$column['key']} ? \Carbon\Carbon::parse($row->{$column['key']})->format($column['format'] ?? 'd-m-Y') : '—' }}</span>

                                {{-- Datetime --}}
                            @elseif(($column['type'] ?? '') === 'datetime')
                                <span
                                    class="text-muted small">{{ $row->{$column['key']} ? \Carbon\Carbon::parse($row->{$column['key']})->format($column['format'] ?? 'd-m-Y H:i') : '—' }}</span>

                                {{-- Currency --}}
                            @elseif(($column['type'] ?? '') === 'currency')
                                <span
                                    class="fw-semibold">{{ $column['currency'] ?? '₹' }}{{ number_format(data_get($row, $column['key'], 0), 2) }}</span>

                                {{-- Image --}}
                            @elseif(($column['type'] ?? '') === 'image')
                                <img src="{{ Storage::url(data_get($row, $column['key'])) }}" alt="NA" class="rounded-circle"
                                     style="width:36px;height:36px;object-fit:cover"/>

                                {{-- Custom render --}}
                            @elseif(isset($column['render']))
                                {!! $column['render']($row) !!}

                                {{-- Default text --}}
                            @else
                                {{ data_get($row, $column['key'], '—') }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($this->visibleColumns) + (count($actions) > 0 ? 1 : 0) }}"
                        class="text-center py-5 text-muted">
                        <div class="d-flex flex-column align-items-center gap-3">
                            <svg width="52" height="52" viewBox="0 0 64 64" fill="none" class="opacity-25">
                                <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="2"/>
                                <path d="M20 42s4-8 12-8 12 8 12 8" stroke="currentColor" stroke-width="2"
                                      stroke-linecap="round"/>
                                <circle cx="24" cy="26" r="3" fill="currentColor"/>
                                <circle cx="40" cy="26" r="3" fill="currentColor"/>
                            </svg>
                            <div>
                                <p class="mb-1 fw-semibold">{{ $emptyMessage }}</p>
                                @if($search || count($activeFilters) > 0)
                                    <button wire:click="clearAllFilters" class="btn btn-sm btn-outline-primary">Clear
                                        filters
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- FOOTER                                                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div
        class="card-footer border-top d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted text-nowrap">Show</span>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width:72px">
                    @foreach($perPageOptions as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
                <span class="small text-muted text-nowrap">entries</span>
            </div>

            <span class="small text-muted">
                Showing <strong>{{ $this->rows->firstItem() ?? 0 }}</strong>–<strong>{{ $this->rows->lastItem() ?? 0 }}</strong>
                of <strong>{{ $this->rows->total() }}</strong> results
            </span>
        </div>

        <div>
            {{ $this->rows->links('utilities.datatable-pagination') }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TOAST                                                        --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
        <div
            x-show="toast"
            x-transition:enter="transition ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:leave="transition ease-in"
            x-transition:leave-end="opacity-0"
            :class="['toast show border-0 text-white', toast?.type === 'error' ? 'bg-danger' : 'bg-success']"
            role="alert"
            style="display:none"
        >
            <div class="d-flex align-items-center">
                <div class="toast-body fw-medium" x-text="toast?.message"></div>
                <button @click="toast=null" type="button" class="btn-close btn-close-white me-2 m-auto"></button>
            </div>
        </div>
    </div>
</div>


