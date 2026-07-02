{{-- resources/views/livewire/admin/assessments/assign-assessment.blade.php --}}

<div>
    {{-- Header --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.assessments.assessment_list') }}">Assessments</a></li>
                <li class="breadcrumb-item active">Assign to Organisations</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">Assign Assessment</h4>
                <p class="text-muted mb-0">Select organisations to assign this assessment</p>
            </div>
            <a href="{{ route('admin.assessments.assessment_list') }}" class="btn btn-outline-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Assessment Info Card --}}
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary-subtle">
            <h5 class="mb-0 text-primary">
                <i class="ri ri-file-list-3-line me-2"></i>Assessment Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mt-3">
                <div class="col-md-2">
                    <small class="text-muted d-block mb-1">Code</small>
                    <span class="badge bg-secondary fs-6">{{ $assessment->assessment_code }}</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block mb-1">Title</small>
                    <strong>{{ $assessment->title }}</strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block mb-1">Assessment Type</small>
                    <span class="badge bg-info-subtle text-info">{{ strtoupper($assessment->assessment_type_id ?? '_') }}</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block mb-1">Age Group</small>
                    <span class="badge bg-warning-subtle text-warning">{{ $assessment->ageGroup->name ?? 'N/A' }}</span>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block mb-1">Difficulty Level</small>
                    <span class="badge bg-danger-subtle text-danger">{{ $assessment->difficultyLevel->level ?? 'N/A' }}</span>
                </div>
                <div class="col-md-2">
                    <small class="text-dark d-block mb-1">Total Questions</small>
                    {{ $assessment->assessment_questions_count  }}
                </div>
                <div class="col-md-2">
                    <small class="text-dark d-block mb-1">Total Marks</small>
                    {{ $assessment->total_marks ?? 0 }}
                </div>
                <div class="col-md-2">
                    <small class="text-dark d-block mb-1">Passing Marks</small>

                    {{ $assessment->passing_marks  }}
                </div>
                @if ($assessment->has_negative_mark)
                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle"
                      data-bs-toggle="tooltip" title="Negative Marking Enabled">
                        <i class="ri ri-subtract-line me-1"></i>Negative Mark
                    </span>
                @endif
                <div class="col-md-2">
                    <small class="text-dark d-block mb-1">Duration</small>
                    {{ $assessment->duration_minutes }} min
                </div>

            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('selectedOrganisations')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri ri-error-warning-line me-2"></i>{{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @enderror

    {{-- Assignment Form --}}
    <form wire:submit.prevent="saveAssignments">
        <div class="row">
            {{-- Organisation Selection --}}
            <div class="col-lg-8">
                <div class="card mb-4  ">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Select Organisations</h5>
                            <span class="badge bg-primary">
                                {{ count($selectedOrganisations) }} Selected
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filters --}}
                        <div class="row g-3 mb-4 pt-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ri ri-search-line"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search organisations..."
                                        wire:model.live.debounce.300ms="search"
                                    >
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" wire:model.live="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" wire:model.live="stateFilter">
                                    <option value="">All States</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select" wire:model.live="perPage">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        {{-- Select All Checkbox --}}
                        <div class="form-check mb-3 pb-3 pt-3 ps-3 bg-light rounded">
                            <input
                                class="form-check-input  ms-0"
                                type="checkbox"
                                id="selectAll"
                                wire:model.live="selectAll"
                            >
                            <label class="form-check-label fw-semibold  ms-2" for="selectAll">
                                Select All Organisations ({{ $organisations->total() }} total)
                            </label>
                        </div>

                        {{-- Organisation List --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th width="50"></th>
                                    <th>Organisation</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($organisations as $organisation)
                                    <tr class="{{ in_array($organisation->id, $selectedOrganisations) ? 'table-primary' : '' }}">
                                        <td>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value="{{ $organisation->id }}"
                                                    {{ in_array($organisation->id, $selectedOrganisations) ? 'checked' : '' }}
                                                    wire:click="toggleOrganisation({{ $organisation->id }})"
                                                    id="org_{{ $organisation->id }}"
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">

                                                <div>
                                                    <div class="fw-semibold">{{ $organisation->name }}</div>
                                                    @if($organisation->email)
                                                        <small class="text-muted">{{ $organisation->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                {{ $organisation->code }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $organisation->organisationType->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $organisation->city ? $organisation->city . ', ' : '' }}
                                                {{ $organisation->state->name ?? '' }}
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'active' => 'success',
                                                    'inactive' => 'warning',
                                                    'suspended' => 'danger'
                                                ];
                                                $color = $statusColors[$organisation->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} text-uppercase">
                                                {{ $organisation->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(in_array($organisation->id, $selectedOrganisations))
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    wire:click="removeAssignment({{ $organisation->id }})"
                                                    wire:confirm="Remove this organisation from assignment?"
                                                >
                                                    <i class="ri ri-close-line"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri ri-building-line" style="font-size: 3rem;"></i>
                                                <p class="mt-2 mb-0">No organisations found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($organisations->hasPages())
                            <div class="mt-3">
                                {{ $organisations->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Assignment Options Sidebar --}}
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Assignment Options</h5>
                    </div>
                    <div class="card-body pt-3">
                        {{-- Selected Count --}}
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="ri ri-information-line fs-4 me-2"></i>
                                <div>
                                    <strong>{{ count($selectedOrganisations) }}</strong> organisation(s) selected
                                </div>
                            </div>
                        </div>

                        {{-- Expiry Date --}}
                        <div class="mb-3">
                            <label for="expiryDate" class="form-label">
                                <i class="ri ri-calendar-line me-1"></i>Expiry Date
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <input
                                type="date"
                                class="form-control @error('expiryDate') is-invalid @enderror"
                                id="expiryDate"
                                wire:model="expiryDate"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            >
                            @error('expiryDate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty for no expiry</small>
                        </div>

                        {{-- Assignment Note --}}
                        <div class="mb-4">
                            <label for="assignmentNote" class="form-label">
                                <i class="ri ri-sticky-note-line me-1"></i>Assignment Note
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <textarea
                                class="form-control"
                                id="assignmentNote"
                                rows="3"
                                wire:model="assignmentNote"
                                placeholder="Add any notes about this assignment..."
                            ></textarea>
                            <small class="text-muted">{{ strlen($assignmentNote) }}/500 characters</small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg"
                                {{ count($selectedOrganisations) === 0 ? 'disabled' : '' }}
                            >
                                <i class="ri ri-save-line me-2"></i>Save Assignments
                            </button>
                            <a href="{{ route('admin.assessments.assessment_list') }}" class="btn btn-outline-secondary">
                                <i class="ri ri-close-line me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('styles')
        <style>
            .table > :not(caption) > * > * {
                padding: 0.75rem;
            }

            .avatar {
                width: 35px;
                height: 35px;
            }

            .avatar-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .form-check-input:checked {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }

            tr.table-primary {
                background-color: rgba(13, 110, 253, 0.1) !important;
            }
        </style>
    @endpush
</div>
