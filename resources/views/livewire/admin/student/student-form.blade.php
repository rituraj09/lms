{{-- resources/views/livewire/admin/student/student-form.blade.php --}}
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
                            Enroll Student
                        </li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-1 text-dark">
                            Enroll New Student
                        </h1>
                        <p class="text-muted mb-0">
                            Register a new student with complete details and credentials
                        </p>
                    </div>
                    <a href="{{ route('admin.org.students', ['organisationId' => Crypt::encrypt($organisationId)]) }}"
                       class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="ri ri-arrow-left-line"></i>
                        <span>Back to Students</span>
                    </a>
                </div>
            </div>
        </div>
        {{-- Add this alert after the page header, before the form --}}
        @if(!$isEditing && !$canEnrollStudent)
            <div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
                <i class="ri ri-error-warning-line fs-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">
                        <i class="ri ri-user-forbid-line me-2"></i>Student Enrollment Limit Reached
                    </h5>
                    <p class="mb-0">{{ $maxStudentMessage }}</p>
                </div>
            </div>
        @endif
        {{-- Form --}}
        <form wire:submit="save" class="needs-validation" novalidate>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 1 — Basic Information
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="ri ri-user-line text-primary" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Basic Information</h6>
                            <p class="text-muted small mb-0">Primary contact details and account information</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Avatar Upload --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Profile Picture</label>
                            <div class="d-flex align-items-center gap-4">

                                {{-- Preview --}}
                                @if ($avatar)
                                    {{-- Newly uploaded file preview --}}
                                    <img src="{{ $avatar->temporaryUrl() }}"
                                         class="rounded-circle border shadow-sm"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         alt="New Avatar Preview">

                                @elseif ($existingAvatarUrl)
                                    {{-- ✅ Use pre-built URL string directly --}}
                                    <img src="{{ $existingAvatarUrl }}"
                                         class="rounded-circle border shadow-sm"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         alt="Current Avatar">

                                @else
                                    {{-- Default placeholder --}}
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border"
                                         style="width: 80px; height: 80px;">
                                        <i class="ri-user-line text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif

                                {{-- Upload Input --}}
                                <div class="flex-grow-1">
                                    <input type="file"
                                           wire:model="avatar"
                                           class="form-control @error('avatar') is-invalid @enderror"
                                           accept="image/*">
                                    @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        <i class="ri-information-line"></i>
                                        @if($existingAvatarUrl && !$avatar)
                                            Current photo shown above. Upload a new one to replace it.
                                        @else
                                            Upload JPG, PNG or GIF. Max size 2MB
                                        @endif
                                    </small>

                                    {{-- Remove Photo Button --}}
                                    @if($existingAvatarUrl && !$avatar)
                                        <button type="button"
                                                wire:click="removeAvatar"
                                                class="btn btn-sm btn-outline-danger mt-2 d-inline-flex align-items-center gap-1">
                                            <i class="ri-delete-bin-line"></i>
                                            Remove Photo
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- Display Name --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Display Name
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-user-3-line text-muted"></i>
                                </span>
                                <input type="text"
                                       wire:model="name"
                                       placeholder="e.g. John Doe"
                                       class="form-control border-start-0 @error('name') is-invalid @enderror" />
                            </div>
                            @error('name')
                            <div class="invalid-feedback d-block">
                                <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Account Status
                                <span class="text-danger">*</span>
                            </label>
                            <select wire:model="status"
                                    class="form-select form-select-lg @error('status') is-invalid @enderror">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Email Address
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-mail-line text-muted"></i>
                                </span>
                                <input type="email"
                                       wire:model="email"
                                       placeholder="student@example.com"
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" />
                            </div>
                            @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Phone Number
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-phone-line text-muted"></i>
                                </span>
                                <input type="text"
                                       wire:model="phone"
                                       placeholder="9876543210"
                                       maxlength="15"
                                       class="form-control border-start-0 @error('phone') is-invalid @enderror" />
                            </div>
                            @error('phone')
                            <div class="invalid-feedback d-block">
                                <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 2 — Personal Details
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="ri ri-id-card-line text-info" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Personal Details</h6>
                            <p class="text-muted small mb-0">Full name and demographic information</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- First Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                First Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   wire:model="first_name"
                                   placeholder="John"
                                   class="form-control form-control-lg @error('first_name') is-invalid @enderror" />
                            @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                Last Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   wire:model="last_name"
                                   placeholder="Doe"
                                   class="form-control form-control-lg @error('last_name') is-invalid @enderror" />
                            @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                Gender
                                <span class="text-danger">*</span>
                            </label>
                            <select wire:model="gender"
                                    class="form-select form-select-lg @error('gender') is-invalid @enderror">
                                <option value="">— Select Gender —</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date of Birth --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                Date of Birth
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   wire:model="date_of_birth"
                                   max="{{ date('Y-m-d') }}"
                                   class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror" />
                            @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 3 — Address Information
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="ri ri-map-pin-line text-success" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Address Information</h6>
                            <p class="text-muted small mb-0">Current residential address</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Address Line 1 --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address Line 1</label>
                            <input type="text"
                                   wire:model="address_line1"
                                   placeholder="House/Flat No., Building Name"
                                   class="form-control form-control-lg @error('address_line1') is-invalid @enderror" />
                            @error('address_line1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address Line 2</label>
                            <input type="text"
                                   wire:model="address_line2"
                                   placeholder="Street, Area, Landmark"
                                   class="form-control form-control-lg @error('address_line2') is-invalid @enderror" />
                            @error('address_line2')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- City --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text"
                                   wire:model="city"
                                   placeholder="Enter city"
                                   class="form-control form-control-lg @error('city') is-invalid @enderror" />
                            @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Postal Code --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Postal Code</label>
                            <input type="text"
                                   wire:model="postal_code"
                                   placeholder="123456"
                                   maxlength="10"
                                   class="form-control form-control-lg @error('postal_code') is-invalid @enderror" />
                            @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- State --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">State</label>
                            <select wire:model.live="state_id"
                                    class="form-select form-select-lg @error('state_id') is-invalid @enderror">
                                <option value="">— Select State —</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- District --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">District</label>
                            <select wire:model="district_id"
                                    class="form-select form-select-lg @error('district_id') is-invalid @enderror"
                                {{ empty($districts) ? 'disabled' : '' }}>
                                <option value="">— Select District —</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(empty($districts))
                                <small class="text-muted">Please select a state first</small>
                            @endif
                        </div>

                        {{-- Country --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text"
                                   wire:model="country"
                                   placeholder="India"
                                   class="form-control form-control-lg @error('country') is-invalid @enderror" />
                            @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 4 — Emergency Contact
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="ri ri-emergency-line text-danger" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Emergency Contact</h6>
                            <p class="text-muted small mb-0">Guardian or emergency contact person</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Emergency Contact Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Contact Name</label>
                            <input type="text"
                                   wire:model="emergency_contact_name"
                                   placeholder="Parent/Guardian Name"
                                   class="form-control form-control-lg @error('emergency_contact_name') is-invalid @enderror" />
                            @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Emergency Contact Phone --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="text"
                                   wire:model="emergency_contact_phone"
                                   placeholder="9876543210"
                                   maxlength="15"
                                   class="form-control form-control-lg @error('emergency_contact_phone') is-invalid @enderror" />
                            @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 5 — Additional Information
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <i class="ri ri-information-line text-warning" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Additional Information</h6>
                            <p class="text-muted small mb-0">Bio and other notes</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Bio --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Bio / Notes</label>
                            <textarea wire:model="bio"
                                      rows="4"
                                      placeholder="Any additional information about the student..."
                                      class="form-control @error('bio') is-invalid @enderror"></textarea>
                            @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 STICKY FORM ACTIONS
            ════════════════════════════════════════════════════ --}}
            <div class="position-sticky bottom-0 z-3 mb-0" style="bottom: 0;">
                <div class="card border-top border-0 shadow-lg rounded-0">
                    <div class="card-body px-4 py-3">
                        <div class="row g-2 align-items-center">

                            {{-- Left: form hint --}}
                            <div class="col-12 col-md">
                                <p class="text-muted small mb-0">
                                    <span class="text-danger fw-semibold">*</span> Indicates required field
                                </p>
                            </div>

                            {{-- Right: buttons --}}
                            <div class="col-12 col-md-auto">
                                <div class="d-flex gap-2 flex-wrap">

                                    {{-- Cancel --}}
                                    <a href="{{ route('admin.org.students', ['organisationId' => Crypt::encrypt($organisationId)]) }}"
                                       class="btn btn-secondary d-inline-flex align-items-center gap-2">
                                        <i class="ri ri-close-line"></i>
                                        <span>Cancel</span>
                                    </a>

                                    {{-- Submit --}}
                                    <button type="submit"
                                            wire:loading.attr="disabled"
                                            {{ (!$isEditing && !$canEnrollStudent) ? 'disabled' : '' }}
                                            class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2 {{ (!$isEditing && !$canEnrollStudent) ? 'disabled' : '' }}">
                                                <span wire:loading.remove wire:target="save">
                                                    <i class="ri {{ $isEditing ? 'ri-save-line' : 'ri-user-add-line' }}"></i>
                                                </span>
                                                                                    <span wire:loading wire:target="save">
                                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                                </span>
                                                                                    <span wire:loading.remove wire:target="save">
                                                    {{ $isEditing ? 'Update Student' : 'Enroll Student' }}
                                                </span>
                                                                                    <span wire:loading wire:target="save">
                                                    {{ $isEditing ? 'Updating...' : 'Enrolling...' }}
                                                </span>
                                    </button>

                                    @if(!$isEditing && !$canEnrollStudent)
                                        <small class="text-danger d-block mt-2">
                                            <i class="ri ri-information-line"></i> Student enrollment is disabled due to maximum limit.
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>

    {{-- ═══════════════════════════════════════════════════
         Password Modal
    ════════════════════════════════════════════════════ --}}
    @if($showPasswordModal)
        <div class="modal fade show d-block"
             tabindex="-1"
             style="background-color: rgba(0,0,0,0.5);"
             x-data="{ show: @entangle('showPasswordModal') }">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="ri ri-checkbox-circle-line fs-4"></i>
                            Student Enrolled Successfully!
                        </h5>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="ri ri-user-add-line text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Student Account Created</h5>
                            <p class="text-muted mb-0">Please note down the credentials below</p>
                        </div>

                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small text-muted fw-semibold d-block mb-1">Student ID</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-2 rounded border">{{ $studentId }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $studentId }}')"
                                                class="btn btn-sm btn-outline-secondary">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small text-muted fw-semibold d-block mb-1">Email</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-2 rounded border">{{ $email }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $email }}')"
                                                class="btn btn-sm btn-outline-secondary">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="small text-muted fw-semibold d-block mb-1">Temporary Password</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="flex-grow-1 bg-white p-2 rounded border">{{ $generatedPassword }}</code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ $generatedPassword }}')"
                                                class="btn btn-sm btn-outline-secondary">
                                            <i class="ri ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0">
                            <i class="ri ri-information-line mt-1"></i>
                            <div class="small">
                                <strong>Important:</strong> This password will be sent to the student's email.
                                Make sure to inform them to change it after first login.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <a href="{{ route('admin.org.students') }}"
                                class="btn btn-primary btn-lg w-100">
                            <i class="ri ri-check-line me-1"></i>
                            Got it, Go to Student List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Toast Notification --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3 z-3">
        <div id="formToast"
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
                    <div>
                        <strong x-text="type === 'success' ? 'Success!' : 'Error!'"></strong>
                        <p x-text="message" class="mb-0 small mt-1"></p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="show = false"></button>
            </div>
        </div>
    </div>

    {{-- Custom CSS --}}
    <style>
        :root {
            --primary: #667eea;
            --primary-light: #eef2f9;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-control-lg,
        .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .input-group-text {
            border-color: #dee2e6;
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 0.875rem;
        }

        .breadcrumb-dots .breadcrumb-item::after {
            content: "•";
            padding: 0 0.5rem;
        }

        .breadcrumb-dots .breadcrumb-item:last-child::after {
            content: "";
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.25em;
        }
    </style>
</div>
