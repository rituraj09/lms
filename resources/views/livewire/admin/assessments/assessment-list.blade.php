{{-- resources/views/livewire/admin/assessments/assessment-list.blade.php --}}

<div>
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Assessments</h4>
            <p class="text-muted mb-0">Manage and assign assessments to organisations</p>
        </div>

    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ri ri-search-line"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-0"
                            placeholder="Search by code, title, or note..."
                            wire:model.live.debounce.300ms="search"
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">All Status</option>
                        <option value="public">Public</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select" wire:model.live="ageGroupFilter">
                        <option value="">All Age Groups</option>
                        @foreach($ageGroups as $ageGroup)
                            <option value="{{ $ageGroup->id }}">{{ $ageGroup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="py-3">Title</th>
                        <th class="py-3">Age Group</th>
                        <th class="py-3">Difficulty Level</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Created By</th>
                        <th class="py-3">Created On</th>
                        <th class="py-3 text-center">Organisations</th>
                        <th class="py-3 text-end px-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($assessments as $assessment)
                        <tr>
                            <td class="px-4">
                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                    {{ $assessment->assessment_code }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $assessment->title }}</div>
                                    @if($assessment->admin_note)
                                        <small class="text-muted">
                                            <i class="ri ri-sticky-note-line"></i>
                                            {{ Str::limit($assessment->admin_note, 40) }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $assessment->ageGroup->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger">
                                    {{ $assessment->difficultyLevel->level ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $statusColors = [
                                        'public' => 'success',
                                        'draft' => 'warning',
                                        'archived' => 'secondary'
                                    ];
                                    $color = $statusColors[$assessment->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} text-uppercase">
                                    {{ $assessment->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">

                                    <small>{{ $assessment->createdBy->name ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $assessment->created_at->format('d M, Y') }}
                                </small>
                            </td>
                            <td class="text-center">

                                    <span class="badge bg-primary-subtle text-primary">
                                            <i class="ri ri-building-line"></i>
                                    {{ $assessment->organisations_count }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group btn-group-sm">

                                    @can('system.assessment.assign')
                                        <a
                                            href="{{ route('admin.assessments.assign', ['encryptedId' => Crypt::encrypt($assessment->id)]) }}"
                                            class="btn btn-outline-success"
                                            data-bs-toggle="tooltip"
                                            title="Assign to Organisations"
                                        >
                                            <i class="ri ri-links-line"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri ri-file-list-line" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">No assessments found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($assessments->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $assessments->firstItem() ?? 0 }} to {{ $assessments->lastItem() ?? 0 }}
                        of {{ $assessments->total() }} assessments
                    </div>
                    <div>
                        {{ $assessments->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Initialize Bootstrap tooltips
            document.addEventListener('livewire:navigated', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
</div>
