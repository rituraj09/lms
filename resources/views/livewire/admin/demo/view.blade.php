<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold">Demo Requests</h4>
            <p class="text-muted mb-0">Manage and track all demo requests submitted from the website</p>
        </div>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-6">
            <i class="ri ri-file-list-3-line me-1"></i> Total: {{ $requests->total() }}
        </span>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="ri ri-time-line text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pending</p>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\DemoRequest::where('status', 'pending')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="ri ri-phone-line text-primary fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Contacted</p>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\DemoRequest::where('status', 'contacted')->count() }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="ri ri-check-double-line text-success fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Closed</p>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\DemoRequest::where('status', 'closed')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ri ri-search-line"></i></span>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="form-control border-start-0"
                            placeholder="Search by name, email, institution, phone...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="contacted">Contacted</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="ri ri-refresh-line"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Name</th>
                            <th>Institution</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>Preferred Slot</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $request)
                            <tr wire:key="request-{{ $request->id }}">
                                <td class="ps-4">{{ $requests->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $request->full_name }}</div>
                                </td>
                                <td>{{ $request->institution }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $request->role }}</span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><i class="ri ri-mail-line text-muted me-1"></i>{{ $request->email }}</div>
                                        <div><i class="ri ri-phone-line text-muted me-1"></i>{{ $request->phone }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($request->preferred_slot)
                                        <span
                                            class="small">{{ \Carbon\Carbon::parse($request->preferred_slot)->format('d M Y, h:i A') }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'contacted' => 'primary',
                                            'closed' => 'success',
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$request->status] ?? 'secondary' }} text-capitalize">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $request->created_at->diffForHumans() }}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button wire:click="viewDetails({{ $request->id }})"
                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ri ri-eye-line"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"
                                                        wire:click.prevent="updateStatus({{ $request->id }}, 'pending')"><i
                                                            class="ri ri-time-line me-2"></i>Mark Pending</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        wire:click.prevent="updateStatus({{ $request->id }}, 'contacted')"><i
                                                            class="ri ri-phone-line me-2"></i>Mark Contacted</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        wire:click.prevent="updateStatus({{ $request->id }}, 'closed')"><i
                                                            class="ri ri-check-double-line me-2"></i>Mark Closed</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        wire:click.prevent="deleteRequest({{ $request->id }})"
                                                        wire:confirm="Are you sure you want to delete this demo request?">
                                                        <i class="ri ri-delete-bin-line me-2"></i>Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="ri ri-inbox-line" style="font-size:48px;color:#cbd5e1;"></i>
                                    <p class="text-muted mt-2 mb-0">No demo requests found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($requests->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Details Modal --}}
    <div wire:ignore.self class="modal fade" id="demoDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="ri ri-file-user-line text-primary me-2"></i> Demo Request Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    @if ($selectedRequest)
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                            <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                style="width:56px;height:56px;font-size:20px;font-weight:600;">
                                {{ strtoupper(substr($selectedRequest->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $selectedRequest->full_name }}</h5>
                                <span class="badge bg-info-subtle text-info mt-1">{{ $selectedRequest->role }}</span>
                            </div>
                            <div class="ms-auto">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'contacted' => 'primary',
                                        'closed' => 'success',
                                    ];
                                @endphp
                                <span
                                    class="badge bg-{{ $statusColors[$selectedRequest->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$selectedRequest->status] ?? 'secondary' }} text-capitalize px-3 py-2">
                                    {{ $selectedRequest->status }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                                        <i class="ri ri-building-line me-1"></i> Institution
                                    </div>
                                    <div class="fw-semibold">{{ $selectedRequest->institution }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                                        <i class="ri ri-mail-line me-1"></i> Email
                                    </div>
                                    <div class="fw-semibold">
                                        <a href="mailto:{{ $selectedRequest->email }}" class="text-decoration-none">
                                            {{ $selectedRequest->email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                                        <i class="ri ri-phone-line me-1"></i> Phone
                                    </div>
                                    <div class="fw-semibold">
                                        <a href="tel:{{ $selectedRequest->phone }}" class="text-decoration-none">
                                            {{ $selectedRequest->phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                                        <i class="ri ri-calendar-event-line me-1"></i> Preferred Slot
                                    </div>
                                    <div class="fw-semibold">
                                        @if ($selectedRequest->preferred_slot)
                                            {{ \Carbon\Carbon::parse($selectedRequest->preferred_slot)->format('d M Y, h:i A') }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded-3">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                                        <i class="ri ri-message-3-line me-1"></i> Message
                                    </div>
                                    <div>{{ $selectedRequest->message ?: 'No message provided.' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div
                                    class="d-flex justify-content-between text-muted small border-top pt-3 mt-2 flex-wrap gap-2">
                                    <span><i class="ri ri-time-line me-1"></i> Submitted:
                                        {{ $selectedRequest->created_at->format('d M Y, h:i A') }}</span>
                                    <span><i class="ri ri-refresh-line me-1"></i> Updated:
                                        {{ $selectedRequest->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if ($selectedRequest)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="ri ri-settings-3-line me-1"></i> Update Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"
                                        wire:click.prevent="updateStatus({{ $selectedRequest->id }}, 'pending')"><i
                                            class="ri ri-time-line me-2"></i>Pending</a></li>
                                <li><a class="dropdown-item" href="#"
                                        wire:click.prevent="updateStatus({{ $selectedRequest->id }}, 'contacted')"><i
                                            class="ri ri-phone-line me-2"></i>Contacted</a></li>
                                <li><a class="dropdown-item" href="#"
                                        wire:click.prevent="updateStatus({{ $selectedRequest->id }}, 'closed')"><i
                                            class="ri ri-check-double-line me-2"></i>Closed</a></li>
                            </ul>
                        </div>
                    @endif
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        $wire.on('show-modal', () => {
            const modalEl = document.getElementById('demoDetailsModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        $wire.on('notify', (event) => {
            // Replace with your toast/alert library if available
            alert(event.message);
        });
    </script>
@endscript
