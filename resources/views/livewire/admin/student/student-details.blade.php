{{-- resources/views/livewire/admin/student/student-details.blade.php --}}
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.org.students', ['organisationId' => Crypt::encrypt($organisationId)]) }}" class="text-decoration-none">
                                <i class="ri ri-graduation-cap-line me-1"></i>Students
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Student Profile
                        </li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-3">
                    <div>
                        <h1 class="h2 fw-bold mb-1 text-dark">
                            Student Profile
                        </h1>
                        <p class="text-muted mb-0">
                            Complete details and information about the student
                        </p>
                    </div>
                    <div class="d-flex gap-2 ">
                        <a href="{{ route('admin.org.students', ['organisationId' => Crypt::encrypt($organisationId)]) }}"
                           class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                            <i class="ri ri-arrow-left-line"></i>
                            <span>Back to List</span>
                        </a>
                        <button wire:click="resetPassword"
                                wire:confirm="Are you sure you want to reset this student's password?"
                                class="btn btn-warning d-inline-flex align-items-center gap-2">
                            <i class="ri ri-lock-password-line"></i>
                            <span>Reset Password</span>
                        </button>
                        <a href="{{ route('admin.org.students.edit', ['organisationId' => Crypt::encrypt($organisationId), 'studentId' => Crypt::encrypt($student->id)]) }}"
                           class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="ri ri-edit-line"></i>
                            <span>Edit Student</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Header --}}
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="bg-gradient-primary text-white p-4 pb-5">
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ $student->avatar_url }}"
                         alt="{{ $student->full_name }}"
                         class="rounded-circle border border-3 border-white shadow"
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-bold">{{ $student->full_name }}</h3>
                        <p class="mb-3 opacity-75 fs-5">{{ $student->details->student_id ?? 'N/A' }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            @php
                                $statusConfig = [
                                    'active' => ['success', 'Active', 'ri-checkbox-circle-line'],
                                    'inactive' => ['secondary', 'Inactive', 'ri-close-circle-line'],
                                    'suspended' => ['danger', 'Suspended', 'ri-forbid-line'],
                                    'pending' => ['warning', 'Pending', 'ri-time-line'],
                                ];
                                [$color, $label, $icon] = $statusConfig[$student->status] ?? ['secondary', 'Unknown', 'ri-question-line'];
                            @endphp
                            <span class="badge bg-{{ $color }} d-inline-flex align-items-center gap-1 px-3 py-2">
                                <i class="ri {{ $icon }}"></i>
                                {{ $label }}
                            </span>
                            @if($student->details && $student->details->gender)
                                <span class="badge bg-white text-dark px-3 py-2">
                                    <i class="ri ri-user-line me-1"></i>
                                    {{ ucfirst($student->details->gender) }}
                                </span>
                            @endif
                            @if($student->details && $student->details->age)
                                <span class="badge bg-white text-dark px-3 py-2">
                                    <i class="ri ri-calendar-line me-1"></i>
                                    {{ $student->details->age }} years old
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column --}}
            <div class="col-12 col-lg-4">

                {{-- Contact Information --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="ri ri-contacts-line text-primary"></i>
                            Contact Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 pt-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-2">
                                    <i class="ri ri-mail-line text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Email</small>
                                    <strong class="d-block text-break">{{ $student->email }}</strong>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded p-2">
                                    <i class="ri ri-phone-line text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Phone</small>
                                    <strong>{{ $student->phone ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Login Credentials Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="ri ri-lock-line text-warning"></i>
                            Login Credentials
                        </h6>
                    </div>
                    <div class="card-body pt-4">

                        <div>
                            <small class="text-muted d-block mb-1">Login Email</small>
                            <code class="d-block bg-light p-2 rounded">{{ $student->email }}</code>
                        </div>
                        <div  class="mt-3">
                            <small class="text-muted d-block mb-1">Phone</small>
                            <code class="d-block bg-light p-2 rounded"> {{ $student->phone }}</code>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-1">Student ID</small>
                            <code class="d-block bg-light p-2 rounded">{{ $student->details->student_id ?? 'N/A' }}</code>
                        </div>
                    </div>
                </div>

                {{-- Organisation --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="ri ri-building-line text-secondary"></i>
                            Organisation Details
                        </h6>
                    </div>
                    <div class="card-body pt-4">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Organisation Name</small>
                            <strong>{{ $student->organisation->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Enrolled On</small>
                            <strong>{{ $student->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">Last Updated</small>
                            <strong>{{ $student->updated_at->format('d M Y, h:i A') }}</strong>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-12 col-lg-8">

                @if($student->details)
                    {{-- Personal Details --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="ri ri-user-line text-info"></i>
                                Personal Details
                            </h6>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">First Name</small>
                                    <strong>{{ $student->details->first_name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">Last Name</small>
                                    <strong>{{ $student->details->last_name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">Date of Birth</small>
                                    <strong>{{ $student->details->date_of_birth ? $student->details->date_of_birth->format('d M Y') : 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">Gender</small>
                                    <strong>{{ $student->details->gender ? ucfirst($student->details->gender) : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="ri ri-map-pin-line text-success"></i>
                                Address Information
                            </h6>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                @if($student->details->address_line1)
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1">Address Line 1</small>
                                        <strong>{{ $student->details->address_line1 }}</strong>
                                    </div>
                                @endif
                                @if($student->details->address_line2)
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1">Address Line 2</small>
                                        <strong>{{ $student->details->address_line2 }}</strong>
                                    </div>
                                @endif
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">City</small>
                                    <strong>{{ $student->details->city ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">Postal Code</small>
                                    <strong>{{ $student->details->postal_code ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">District</small>
                                    <strong>{{ $student->details->district->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">State</small>
                                    <strong>{{ $student->details->state->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Country</small>
                                    <strong>{{ $student->details->country ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Emergency Contact --}}
                    @if($student->details->emergency_contact_name || $student->details->emergency_contact_phone)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                    <i class="ri ri-emergency-line text-danger"></i>
                                    Emergency Contact
                                </h6>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-4">
                                    @if($student->details->emergency_contact_name)
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted d-block mb-1">Contact Name</small>
                                            <strong>{{ $student->details->emergency_contact_name }}</strong>
                                        </div>
                                    @endif
                                    @if($student->details->emergency_contact_phone)
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted d-block mb-1">Contact Phone</small>
                                            <strong>{{ $student->details->emergency_contact_phone }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Bio --}}
                    @if($student->details->bio)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                    <i class="ri ri-information-line text-warning"></i>
                                    Bio / Notes
                                </h6>
                            </div>
                            <div class="card-body pt-4">
                                <p class="mb-0">{{ $student->details->bio }}</p>
                            </div>
                        </div>
                    @endif
                @endif

            </div>
        </div>

    </div>

    {{-- Reset Password Modal --}}
    @if($showResetPasswordModal)
        <div class="modal fade show d-block"
             tabindex="-1"
             style="background-color: rgba(0,0,0,0.5);"
             x-data="{ show: @entangle('showResetPasswordModal') }"
             x-show="show"
             x-transition>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-white border-0">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="ri ri-lock-password-line fs-4"></i>
                            Password Reset Successful
                        </h5>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="ri ri-lock-password-line text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">New Password Generated</h5>
                            <p class="text-muted mb-0">Please share this password with the student securely</p>
                        </div>

                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small text-muted fw-semibold d-block mb-2">Student Name</label>
                                    <div class="bg-white p-3 rounded border">
                                        <strong>{{ $student->full_name }}</strong>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small text-muted fw-semibold d-block mb-2">Student ID</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-3 rounded border fs-6">{{ $student->details->student_id }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $student->details->student_id }}')"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Copy to clipboard">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small text-muted fw-semibold d-block mb-2">Email</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-3 rounded border fs-6">{{ $student->email }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $student->email }}')"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Copy to clipboard">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="small text-muted fw-semibold d-block mb-2">New Password</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-3 rounded border fs-6 text-danger fw-bold">{{ $newPassword }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $newPassword }}')"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Copy to clipboard">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0">
                            <i class="ri ri-alarm-warning-line mt-1 fs-5"></i>
                            <div class="small">
                                <strong>Important:</strong> Make sure to save this password before closing this window.
                                It cannot be retrieved later. The student should change this password after first login.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button"
                                wire:click="closeResetPasswordModal"
                                class="btn btn-warning btn-lg w-100">
                            <i class="ri ri-check-line me-1"></i>
                            I've Saved the Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
             x-transition
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
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
    </style>
</div>
