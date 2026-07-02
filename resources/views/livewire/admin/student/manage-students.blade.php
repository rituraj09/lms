{{-- resources/views/livewire/admin/student/manage-students.blade.php --}}
<div class="min-h-screen bg-light py-4 py-lg-5">
    <div class="container-xl">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.org.dashboard', ['organisationId' => Crypt::encrypt($organisationId)]) }}" class="text-decoration-none">
                                <i class="ri ri-dashboard-line me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Student list
                        </li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-3">
                    <div>
                        <h1 class="h2 fw-bold mb-1 text-dark">
                            <i class="ri ri-graduation-cap-line me-2"></i>Student Management
                        </h1>
                        <p class="text-muted mb-0">
                            Manage and monitor all students in your organisation
                        </p>
                    </div>
                    @if($canEnrollStudent)
                        <a href="{{ route('admin.org.students.create', ['organisationId' => Crypt::encrypt($organisationId)]) }}"
                           class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2">
                            <i class="ri ri-user-add-line"></i>
                            <span>Enroll Student</span>
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg d-inline-flex align-items-center gap-2" disabled>
                            <i class="ri ri-user-forbid-line"></i>
                            <span>Enrollment Limit Reached</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @if($maxStudentCount > 0)
            <div class="alert {{ $canEnrollStudent ? 'alert-info' : 'alert-danger' }} d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri {{ $canEnrollStudent ? 'ri-information-line' : 'ri-error-warning-line' }} fs-5"></i>
                    <span>
                <strong>Student Enrollment:</strong>
                {{ $currentStudentCount }} / {{ $maxStudentCount }} students enrolled
                @if(!$canEnrollStudent)
                            <span class="badge bg-danger ms-2">Limit Reached</span>
                        @endif
            </span>
                </div>
                @if($canEnrollStudent)
                    <small class="text-muted">{{ $maxStudentCount - $currentStudentCount }} slots remaining</small>
                @endif
            </div>
        @endif
        {{-- Filters & Search --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ri ri-search-line text-muted"></i>
                            </span>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   class="form-control border-start-0"
                                   placeholder="Search by name, email, phone or student ID...">
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <select wire:model.live="statusFilter" class="form-select form-select-lg">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <select wire:model.live="perPage" class="form-select form-select-lg">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <button wire:click="$set('search', '')" class="btn btn-outline-secondary btn-lg w-100">
                            <i class="ri ri-refresh-line me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 fw-semibold">#</th>
                            <th class="px-4 py-3 fw-semibold">Student ID</th>
                            <th class="px-4 py-3 fw-semibold">Student Name</th>
                            <th class="px-4 py-3 fw-semibold">Contact</th>
                            <th class="px-4 py-3 fw-semibold">Status</th>
                            <th class="px-4 py-3 fw-semibold">Enrolled On</th>
                            <th class="px-4 py-3 fw-semibold text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($students as $student)

                            <tr wire:key="student-{{ $student->id }}" class="student-row">
                                <td class="px-4 py-3">
                                    {{ $students->firstItem() + $loop->index }}
                                </td>
                                <td class="px-4 py-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2">
                                            {{ $student->details->student_id ?? 'N/A' }}
                                        </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $student->name }}
            </td>
            <td class="px-4 py-3">
                <div class="small">
                    <div class="text-dark mb-1">
                        <i class="ri ri-mail-line text-muted me-1"></i>
                        {{ $student->email }}
                                        </div>
                                        @if($student->phone)
                                            <div class="text-muted">
                                                <i class="ri ri-phone-line me-1"></i>
                                                {{ $student->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusConfig = [
                                            'active' => ['success', 'Active', 'ri-checkbox-circle-line'],
                                            'inactive' => ['secondary', 'Inactive', 'ri-close-circle-line'],
                                            'suspended' => ['danger', 'Suspended', 'ri-forbid-line'],
                                            'pending' => ['warning', 'Pending', 'ri-time-line'],
                                        ];
                                        [$color, $label, $icon] = $statusConfig[$student->status] ?? ['secondary', 'Unknown', 'ri-question-line'];
                                    @endphp
                                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} d-inline-flex align-items-center gap-1 px-3 py-2">
                                            <i class="ri {{ $icon }}"></i>
                                            {{ $label }}
                                        </span>
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">
                                        {{ $student->created_at->format('d M Y') }}
                                    </small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.org.students.view', ['organisationId' => Crypt::encrypt($organisationId), 'studentId' => Crypt::encrypt($student->id)]) }}"
                                           class="btn btn-outline-primary"
                                           title="View Details">
                                            <i class="ri ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('admin.org.students.edit', ['organisationId' => Crypt::encrypt($organisationId), 'studentId' => Crypt::encrypt($student->id)]) }}"
                                           class="btn btn-outline-info"
                                           title="Edit">
                                            <i class="ri ri-edit-line"></i>
                                        </a>
                                        <button wire:click="deleteStudent({{ $student->id }})"
                                                wire:confirm="Are you sure you want to delete this student?"
                                                class="btn btn-outline-danger"
                                                title="Delete">
                                            <i class="ri ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="ri ri-user-search-line text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3 mb-0">No students found</p>
                                        @if($search)
                                            <button wire:click="$set('search', '')" class="btn btn-sm btn-link">
                                                Clear search
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($students->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Toast Notification --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="notifyToast"
             x-data="{
                show: false,
                message: '',
                type: 'success',
                init() {
                    window.addEventListener('notify', (e) => {
                        this.message = e.detail.message;
                        this.type = e.detail.type ?? 'success';
                        this.show = true;
                        setTimeout(() => this.show = false, 3500);
                    });
                }
             }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="toast align-items-center border-0 shadow-lg"
             :class="type === 'success' ? 'bg-success' : 'bg-danger'"
             role="alert"
             style="display: none;">
            <div class="d-flex align-items-center text-white">
                <div class="toast-body d-flex align-items-center gap-2">
                    <template x-if="type === 'success'">
                        <i class="ri ri-checkbox-circle-line fs-5"></i>
                    </template>
                    <template x-if="type === 'error'">
                        <i class="ri ri-close-circle-line fs-5"></i>
                    </template>
                    <span x-text="message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="show = false"></button>
            </div>
        </div>
    </div>

    <style>
        .student-row {
            transition: all 0.2s ease;
        }

        .student-row:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
    </style>
</div>
