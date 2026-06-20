{{-- resources/views/livewire/admin/admin-management/admin-form.blade.php --}}
<div class="min-h-screen bg-light py-4 py-lg-5">
    <div class="container-xl">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.admins.index') }}"  class="text-decoration-none">
                                <i class="ri ri-admin-line me-1"></i>Admin Management
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $isEditing ? 'Edit Admin' : 'Create Admin' }}
                        </li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-1 text-dark">
                            {{ $isEditing ? 'Edit Admin User' : 'Create New Admin' }}
                        </h1>
                        <p class="text-muted mb-0">
                            {{ $isEditing
                                ? 'Update the admin user details and permissions.'
                                : 'Register a new administrator with role and organisation access.' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.admins.index') }}"
                       class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="ri ri-arrow-left-line"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form wire:submit="save" class="needs-validation" novalidate>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 1 — Basic Account Information
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ri ri-user-settings-line text-primary" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Account Information</h6>
                            <p class="text-muted small mb-0">Basic login credentials and contact details</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Username --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Username
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-user-line text-muted"></i>
                                </span>
                                <input type="text" wire:model.live="name"
                                       placeholder="e.g. john.doe"
                                       class="form-control border-start-0
                                              @error('name') is-invalid @enderror" />
                                @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email Address --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Email Address
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-mail-line text-muted"></i>
                                </span>
                                <input type="email" wire:model="email"
                                       placeholder="admin@example.com"
                                       class="form-control border-start-0
                                              @error('email') is-invalid @enderror" />
                                @error('email')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Mobile Number
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-phone-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="mobile"
                                       placeholder="9876543210"
                                       inputmode="numeric"
                                       maxlength="10"
                                       class="form-control border-start-0
                                              @error('mobile') is-invalid @enderror" />
                                @error('mobile')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="ri ri-information-line"></i> 10-digit mobile number required
                            </small>
                        </div>

                        {{-- Status --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Account Status
                                <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                @foreach([
                                    'active'    => ['success', 'Active',    'ri ri-checkbox-circle-line'],
                                    'inactive'  => ['warning', 'Inactive',  'ri ri-pause-circle-line'],
                                    'suspended' => ['danger',  'Suspended', 'ri ri-forbid-2-line'],
                                ] as $val => [$color, $label, $icon])
                                    <input type="radio" class="btn-check" name="status"
                                           id="status{{ ucfirst($val) }}" wire:model="status"
                                           value="{{ $val }}" />
                                    <label class="btn btn-outline-{{ $color }} btn-sm fw-semibold
                                                  d-flex align-items-center justify-content-center gap-2"
                                           for="status{{ ucfirst($val) }}">
                                        <i class="{{ $icon }}"></i>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('status')
                            <div class="invalid-feedback d-block mt-2">
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
                        <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ri ri-id-card-line text-info" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Personal Details</h6>
                            <p class="text-muted small mb-0">Name and designation information</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- First Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-user-3-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="first_name"
                                       placeholder="John"
                                       class="form-control border-start-0
                                              @error('first_name') is-invalid @enderror" />
                                @error('first_name')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Last Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-user-3-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="last_name"
                                       placeholder="Doe"
                                       class="form-control border-start-0
                                              @error('last_name') is-invalid @enderror" />
                                @error('last_name')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Designation --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Designation</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-briefcase-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="designation"
                                       placeholder="e.g. Senior Administrator, Support Manager"
                                       class="form-control border-start-0
                                              @error('designation') is-invalid @enderror" />
                                @error('designation')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 3 — Security & Permissions
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ri ri-shield-check-line text-warning" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Security & Permissions</h6>
                            <p class="text-muted small mb-0">Password and role assignments</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Password Section Header --}}
                        @if(!$isEditing)
                            <div class="col-12">
                                <h6 class="fw-semibold text-dark mb-3">
                                    <i class="ri ri-lock-line me-2"></i>Set Password
                                </h6>
                            </div>
                        @endif

                        {{-- Password --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                @if($isEditing)
                                    New Password
                                    <span class="small text-muted">(Leave blank to keep current)</span>
                                @else
                                    Password
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-lock-line text-muted"></i>
                                </span>
                                <input type="password" wire:model="password"
                                       placeholder="Enter secure password"
                                       class="form-control border-start-0
                                              @error('password') is-invalid @enderror" />
                                @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- Password Requirements --}}
                            @if(!$isEditing && empty($password))
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <p class="small fw-semibold text-dark mb-2">
                                        <i class="ri ri-information-line text-primary"></i>
                                        Password must contain:
                                    </p>
                                    <ul class="small mb-0 ps-3">
                                        <li class="mb-1">
                                            <span class="badge bg-light text-muted">8-16 characters</span>
                                        </li>
                                        <li class="mb-1">
                                            <span class="badge bg-light text-muted">One uppercase letter</span>
                                        </li>
                                        <li class="mb-1">
                                            <span class="badge bg-light text-muted">One lowercase letter</span>
                                        </li>
                                        <li class="mb-1">
                                            <span class="badge bg-light text-muted">One number</span>
                                        </li>
                                        <li>
                                            <span class="badge bg-light text-muted">One special character</span>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($password)
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <p class="small fw-semibold text-dark mb-2">
                                        <i class="ri ri-information-line text-primary"></i>
                                        Password requirements:
                                    </p>
                                    <ul class="small mb-0 ps-3">
                                        <li class="mb-1">
                                            <i class="ri ri-checkbox-circle-line text-success"></i>
                                            8-16 characters
                                        </li>
                                        <li class="mb-1">
                                            <i class="ri ri-checkbox-circle-line text-success"></i>
                                            One uppercase letter
                                        </li>
                                        <li class="mb-1">
                                            <i class="ri ri-checkbox-circle-line text-success"></i>
                                            One lowercase letter
                                        </li>
                                        <li class="mb-1">
                                            <i class="ri ri-checkbox-circle-line text-success"></i>
                                            One number
                                        </li>
                                        <li>
                                            <i class="ri ri-checkbox-circle-line text-success"></i>
                                            One special character
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                @if($isEditing)
                                    Confirm New Password
                                @else
                                    Confirm Password
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-lock-line text-muted"></i>
                                </span>
                                <input type="password" wire:model="password_confirmation"
                                       placeholder="Confirm your password"
                                       class="form-control border-start-0
                                              @error('password_confirmation') is-invalid @enderror" />
                                @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Separator --}}
                        <div class="col-12">
                            <hr class="my-2">
                        </div>

                        {{-- Role Section Header --}}
                        <div class="col-12">
                            <h6 class="fw-semibold text-dark mb-3">
                                <i class="ri ri-shield-star-line me-2"></i>Role Assignment
                            </h6>
                        </div>

                        {{-- Role --}}
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                Admin Role
                                <span class="text-danger">*</span>
                            </label>
                            <select wire:model="role_id"
                                    class="form-select form-select-lg
                                       @error('role_id') is-invalid @enderror">
                                <option value="0">— Select Role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <div class="invalid-feedback d-block">
                                <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                <i class="ri ri-information-line"></i>
                                Select the primary role for this admin user
                            </small>
                        </div>

                        {{-- Organisations Selection --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-building-line me-2"></i>Organisations Access
                            </label>
                            <p class="text-muted small mb-3">
                                Select the organisations this admin can manage. Leave empty for full access.
                            </p>

                            @if($organisations->isEmpty())
                                <div class="alert alert-info">
                                    <i class="ri ri-information-line me-2"></i>
                                    No organisations available. Create organisations first.
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($organisations as $org)
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input cursor-pointer"
                                                       type="checkbox"
                                                       wire:model.live="organisation_ids"
                                                       value="{{ $org->id }}"
                                                       id="org{{ $org->id }}" />
                                                <label class="form-check-label cursor-pointer w-100 ps-2"
                                                       for="org{{ $org->id }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        {{-- Logo --}}
                                                        @if($org->logo)
                                                            <img src="{{ Storage::url($org->logo) }}"
                                                                 alt="{{ $org->name }}"
                                                                 class="rounded"
                                                                 style="width: 24px; height: 24px; object-fit: cover;" />
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                 style="width: 24px; height: 24px; font-size: 0.65rem;">
                                                                <span class="fw-bold text-muted">
                                                                    {{ strtoupper(substr($org->name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <strong class="d-block" style="font-size: 0.9rem;">
                                                                {{ $org->name }}
                                                            </strong>
                                                            <small class="text-muted d-block">
                                                                @if($org->organisationType)
                                                                    {{ $org->organisationType->name }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @error('organisation_ids')
                            <div class="invalid-feedback d-block mt-2">
                                <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror

                            {{-- Selected Count --}}
                            @if(!empty($organisation_ids))
                                <div class="alert alert-success alert-sm mt-3 d-flex align-items-center gap-2 mb-0">
                                    <i class="ri ri-checkbox-multiple-mark-line"></i>
                                    <span>{{ count($organisation_ids) }} organisation(s) selected</span>
                                </div>
                            @endif
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
                                    <a href="{{ route('admin.admins.index') }}"
                                       class="btn btn-secondary d-inline-flex align-items-center gap-2">
                                        <i class="ri ri-close-line"></i>
                                        <span>Cancel</span>
                                    </a>

                                    {{-- Submit --}}
                                    <button type="submit"
                                            wire:loading.attr="disabled"
                                            class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2">

                                        {{-- Spinner when loading --}}
                                        <span wire:loading wire:target="save">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        </span>

                                        {{-- Icon when idle --}}
                                        <span wire:loading.remove wire:target="save">
                                            @if($isEditing)
                                                <i class="ri ri-save-line"></i>
                                            @else
                                                <i class="ri ri-add-circle-line"></i>
                                            @endif
                                        </span>

                                        <span wire:loading.remove wire:target="save">
                                            {{ $isEditing ? 'Update Admin' : 'Create Admin' }}
                                        </span>
                                        <span wire:loading wire:target="save">
                                            {{ $isEditing ? 'Updating...' : 'Creating...' }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>

    {{-- ═══════════════════════════════════════════════════
         Toast Notification
    ════════════════════════════════════════════════════ --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3 z-3">
        <div id="formToast"
             x-data="{
                show: false,
                message: '',
                type: 'success',
                init() {
                    window.addEventListener('notify', (e) => {
                        this.message = e.detail.message;
                        this.type    = e.detail.type ?? 'success';
                        this.show    = true;
                        setTimeout(() => this.show = false, 3500);
                    });
                }
            }"
             x-show="show"
             x-transition
             class="toast align-items-center border-0 shadow-lg"
             :class="type === 'success' ? 'bg-success' : 'bg-danger'"
             role="alert"
             aria-live="assertive"
             aria-atomic="true"
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
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        @click="show = false" aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Custom CSS for Bootstrap enhancements --}}
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
        }

        .card {
            border-radius: 0.875rem;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .form-check-label {
            padding-top: 0.125rem;
        }

        .form-check-input {
            cursor: pointer;
            border: 2px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }

        .breadcrumb-dots .breadcrumb-item::after {
            content: "•";
            padding: 0 0.5rem;
        }

        .breadcrumb-dots .breadcrumb-item:last-child::after {
            content: "";
        }

        {{-- Status button groups --}}
        .btn-group .btn-check:checked + .btn {
            font-weight: 600;
        }

        {{-- Smooth transitions --}}
        .btn, .form-control, .form-select {
            transition: all 0.2s ease;
        }

        {{-- Password requirements badges --}}
        .badge {
            font-weight: 500;
            padding: 0.35rem 0.65rem;
        }

        {{-- Alert styling --}}
        .alert-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        {{-- Organization card styling --}}
        .form-check {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .form-check:has(.form-check-input:checked) {
            background-color: var(--primary-light);
            border-color: var(--primary);
        }

        {{-- Input group text styling --}}
        .input-group-text {
            background-color: #f8f9fa;
        }

        {{-- Header icons styling --}}
        .card-header {
            background-color: #f8f9fa;
        }

        .card-header h6 {
            color: #212529;
        }

        {{-- Loading spinner --}}
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.25em;
        }
    </style>
</div>
