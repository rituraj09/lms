{{-- resources/views/livewire/admin/organisation/organisation-form.blade.php --}}
<div class="min-h-screen bg-light py-4 py-lg-5">
    <div class="container-xl">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dots mb-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.organisations.index') }}" wire:navigate class="text-decoration-none">
                                <i class="ri ri-home-4-line me-1"></i>Organisations
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $isEditing ? 'Edit Organisation' : 'Create Organisation' }}
                        </li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-1 text-dark">
                            {{ $isEditing ? 'Edit ' . $organisation->name : 'Create Organisation' }}
                        </h1>
                        <p class="text-muted mb-0">
                            {{ $isEditing
                                ? 'Update the organisation details and save your changes.'
                                : 'Register a new organisation by filling in the required information.' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.organisations.index') }}" wire:navigate
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="ri ri-arrow-left-line"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form wire:submit="save" enctype="multipart/form-data" class="needs-validation" novalidate>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 1 — Banner & Logo Upload
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">

                {{-- Banner Preview --}}
                <div class="position-relative overflow-hidden"
                    style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);">

                    {{-- Existing / Preview Banner --}}
                    @if ($banner)
                        <img src="{{ $banner->temporaryUrl() }}" alt="Banner Preview"
                            class="w-100 h-100 object-fit-cover" />
                    @elseif($existingBanner)
                        <img src="{{ Storage::url($existingBanner) }}" alt="Banner"
                            class="w-100 h-100 object-fit-cover" />
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <p class="text-white-50 small fw-semibold text-uppercase letter-spacing-1 mb-0">
                                Organisation Banner
                            </p>
                        </div>
                    @endif

                    {{-- Banner Upload Overlay --}}
                    <label for="bannerInput"
                        class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column
                               align-items-center justify-content-center bg-dark bg-opacity-40
                               cursor-pointer opacity-0 hover-opacity-100 transition"
                        style="transition: opacity 200ms ease;">
                        <i class="ri ri-camera-3-line text-white mb-2" style="font-size: 2rem;"></i>
                        <span class="text-white small fw-semibold">
                            {{ $existingBanner || $banner ? 'Change Banner' : 'Upload Banner' }}
                        </span>
                        <span class="text-white-50 x-small mt-1">Max 5MB · JPG, PNG, WEBP</span>
                    </label>
                    <input id="bannerInput" type="file" wire:model="banner" accept="image/*" class="d-none" />
                </div>

                {{-- Logo + Name Row --}}
                <div class="card-body position-relative">
                    <div class="d-flex align-items-end gap-4">

                        {{-- Logo Upload --}}
                        <div class="position-relative flex-shrink-0 group" style="margin-top: -80px;">
                            <div class="bg-indigo-light rounded-3 overflow-hidden shadow-sm"
                                style="width: 120px; height: 120px; border: 4px solid white; background: #eef2f9;">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview"
                                        class="w-100 h-100 object-fit-cover" />
                                @elseif($existingLogo)
                                    <img src="{{ Storage::url($existingLogo) }}" alt="Logo"
                                        class="w-100 h-100 object-fit-cover" />
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <span class="fw-bold text-indigo" style="font-size: 2.5rem;">
                                            {{ $name ? strtoupper(substr($name, 0, 2)) : 'ORG' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Logo Overlay --}}
                            <label for="logoInput"
                                class="position-absolute top-0 start-0 w-100 h-100 rounded-3
                                       d-flex align-items-center justify-content-center
                                       bg-dark bg-opacity-50 opacity-0 hover-opacity-100
                                       cursor-pointer"
                                style="transition: opacity 200ms ease;">
                                <i class="ri ri-camera-3-line text-white" style="font-size: 1.5rem;"></i>
                            </label>
                            <input id="logoInput" type="file" wire:model="logo" accept="image/*" class="d-none" />
                        </div>

                        {{-- Name hint --}}
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ $name ?: 'Organisation Name' }}
                            </h5>
                            <p class="text-muted small mb-0">
                                <i class="ri ri-information-line"></i>
                                Click the logo or banner to upload images
                            </p>
                        </div>
                    </div>

                    {{-- Upload Errors --}}
                    <div class="mt-3">
                        @error('logo')
                            <div class="alert alert-danger alert-sm d-flex align-items-center gap-2 mb-2">
                                <i class="ri ri-error-warning-line"></i>
                                <span class="small">Logo: {{ $message }}</span>
                            </div>
                        @enderror
                        @error('banner')
                            <div class="alert alert-danger alert-sm d-flex align-items-center gap-2 mb-0">
                                <i class="ri ri-error-warning-line"></i>
                                <span class="small">Banner: {{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 2 — Basic Information
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-indigo-light rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="ri ri-building-2-line text-indigo" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Basic Information</h6>
                            <p class="text-muted small mb-0">Core details about the organisation</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Organisation Name --}}
                        <div class="col-12 col-lg-8">
                            <label class="form-label fw-semibold">
                                Organisation Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" wire:model.live="name" placeholder="e.g. Greenfield Academy"
                                class="form-control form-control-lg
                                          @error('name') is-invalid @enderror" />
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Organisation Type --}}
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">
                                Organisation Type
                                <span class="text-danger">*</span>
                            </label>
                            <select wire:model="organisation_type_id"
                                class="form-select form-select-lg
                                       @error('organisation_type_id') is-invalid @enderror">
                                <option value="0">— Select Type —</option>
                                @foreach ($organisationTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('organisation_type_id')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-mail-line text-muted"></i>
                                </span>
                                <input type="email" wire:model="email" placeholder="info@organisation.com"
                                    class="form-control border-start-0
                                              @error('email') is-invalid @enderror" />
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-phone-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="phone" placeholder="+91 98765 43210"
                                    class="form-control border-start-0
                                              @error('phone') is-invalid @enderror" />
                                @error('phone')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Website --}}
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">Website</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-global-line text-muted"></i>
                                </span>
                                <input type="url" wire:model="website" placeholder="https://www.example.com"
                                    class="form-control border-start-0
                                              @error('website') is-invalid @enderror" />
                                @error('website')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">
                                Status
                                <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                @foreach ([
        'active' => ['success', 'Active', 'ri ri-checkbox-circle-line'],
        'inactive' => ['warning', 'Inactive', 'ri ri-pause-circle-line'],
        'suspended' => ['danger', 'Suspended', 'ri ri-forbid-2-line'],
    ] as $val => [$color, $label, $icon])
                                    <input type="radio" class="btn-check" name="status"
                                        id="status{{ ucfirst($val) }}" wire:model="status"
                                        value="{{ $val }}" />
                                    <label
                                        class="btn btn-outline-{{ $color }} btn-sm fw-semibold
                                                  d-flex align-items-center justify-content-center gap-2"
                                        for="status{{ ucfirst($val) }}">
                                        <i class="{{ $icon }}"></i>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Max Students --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">
                                Max Students
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-graduation-cap-line text-muted"></i>
                                </span>
                                <input type="number" wire:model="max_students" min="0" placeholder="500"
                                    class="form-control border-start-0
                                              @error('max_students') is-invalid @enderror" />
                                @error('max_students')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <div class="position-relative">
                                <textarea wire:model="description" rows="4" placeholder="Brief description of the organisation..."
                                    class="form-control form-control-lg
                                                 @error('description') is-invalid @enderror"
                                    style="resize: vertical;"></textarea>
                                <small class="d-block text-muted mt-2">
                                    <i class="ri ri-information-line"></i>
                                    {{ strlen($description) }}/1000 characters
                                </small>
                                @error('description')
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
                 SECTION 3 — Address Details
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="ri ri-map-pin-line text-success" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Address Details</h6>
                            <p class="text-muted small mb-0">Physical location of the organisation</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Address Line 1 --}}
                        <div class="col-12 col-lg-8">
                            <label class="form-label fw-semibold">Address Line 1</label>
                            <input type="text" wire:model="address_line1"
                                placeholder="Building name, Street number..."
                                class="form-control form-control-lg
                                          @error('address_line1') is-invalid @enderror" />
                            @error('address_line1')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">Address Line 2</label>
                            <input type="text" wire:model="address_line2" placeholder="Area, Locality..."
                                class="form-control form-control-lg
                                          @error('address_line2') is-invalid @enderror" />
                            @error('address_line2')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- State --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">State</label>
                            <select wire:model.live="state_id"
                                class="form-select form-select-lg
                                       @error('state_id') is-invalid @enderror">
                                <option value="0">— Select State —</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- District --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">District</label>
                            <div class="position-relative">
                                <select wire:model="district_id" @disabled(!$state_id)
                                    class="form-select form-select-lg
                                           @error('district_id') is-invalid @enderror">
                                    <option value="0">
                                        {{ $state_id ? '— Select District —' : '— Select State First —' }}
                                    </option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>

                                {{-- Loading districts --}}
                                <div wire:loading wire:target="updatedStateId"
                                    class="position-absolute top-50 end-3 translate-middle-y">
                                    <div class="spinner-border spinner-border-sm text-indigo" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            @error('district_id')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- City --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" wire:model="city" placeholder="City name"
                                class="form-control form-control-lg
                                          @error('city') is-invalid @enderror" />
                            @error('city')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Country --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" wire:model="country" placeholder="India"
                                class="form-control form-control-lg
                                          @error('country') is-invalid @enderror" />
                            @error('country')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Postal Code --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Postal Code</label>
                            <input type="text" wire:model="postal_code" placeholder="600001"
                                class="form-control form-control-lg
                                          @error('postal_code') is-invalid @enderror" />
                            @error('postal_code')
                                <div class="invalid-feedback d-block">
                                    <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 SECTION 4 — Contact Person
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="ri ri-user-line text-primary" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Contact Person</h6>
                            <p class="text-muted small mb-0">Primary point of contact for this organisation</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Contact Person Name --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Full Name</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-user-3-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="contact_person" placeholder="John Doe"
                                    class="form-control border-start-0
                                              @error('contact_person') is-invalid @enderror" />
                                @error('contact_person')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Contact Person Phone --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Phone</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-phone-line text-muted"></i>
                                </span>
                                <input type="text" wire:model="contact_person_phone" placeholder="+91 98765 43210"
                                    class="form-control border-start-0
                                              @error('contact_person_phone') is-invalid @enderror" />
                                @error('contact_person_phone')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Contact Person Email --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-mail-line text-muted"></i>
                                </span>
                                <input type="email" wire:model="contact_person_email"
                                    placeholder="contact@example.com"
                                    class="form-control border-start-0
                                              @error('contact_person_email') is-invalid @enderror" />
                                @error('contact_person_email')
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
                 SECTION 5 — Subscription Period
            ════════════════════════════════════════════════════ --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="ri ri-calendar-event-line text-warning" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Subscription Period</h6>
                            <p class="text-muted small mb-0">Define the active subscription window</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Subscription Start --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-calendar-line text-muted"></i>
                                </span>
                                <input type="date" wire:model="subscription_start"
                                    class="form-control border-start-0
                                              @error('subscription_start') is-invalid @enderror" />
                                @error('subscription_start')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Subscription End --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="ri ri-calendar-check-line text-muted"></i>
                                </span>
                                <input type="date" wire:model="subscription_end"
                                    class="form-control border-start-0
                                              @error('subscription_end') is-invalid @enderror" />
                                @error('subscription_end')
                                    <div class="invalid-feedback d-block">
                                        <i class="ri ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Subscription Duration Preview --}}
                    @if ($subscription_start && $subscription_end)
                        @php
                            $start = \Carbon\Carbon::parse($subscription_start);
                            $end = \Carbon\Carbon::parse($subscription_end);
                            $days = $start->diffInDays($end, false);
                            $months = $start->diffInMonths($end);
                        @endphp

                        @if ($days >= 0)
                            <div class="alert alert-warning alert-sm mt-3 d-flex align-items-center gap-2 mb-0">
                                <i class="ri ri-information-line"></i>
                                <div>
                                    <strong>Subscription Duration:</strong>
                                    {{ $months > 0 ? $months . ' month' . ($months > 1 ? 's' : '') . ' ' : '' }}
                                    {{ $days % 30 }} day{{ $days % 30 !== 1 ? 's' : '' }}
                                    <span class="text-muted small">({{ $days }} days total)</span>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger alert-sm mt-3 d-flex align-items-center gap-2 mb-0">
                                <i class="ri ri-alert-line"></i>
                                <span>End date must be after start date.</span>
                            </div>
                        @endif
                    @endif
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
                                    <a href="{{ route('admin.organisations.index') }}" wire:navigate
                                        class="btn btn-secondary d-inline-flex align-items-center gap-2">
                                        <i class="ri ri-close-line"></i>
                                        <span>Cancel</span>
                                    </a>

                                    {{-- Save Draft (Edit only) --}}
                                    @if ($isEditing)
                                        <button type="button"
                                            class="btn btn-light d-inline-flex align-items-center gap-2 border">
                                            <i class="ri ri-draft-line"></i>
                                            <span>Save as Draft</span>
                                        </button>
                                    @endif

                                    {{-- Submit --}}
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2">

                                        {{-- Spinner when loading --}}
                                        <span wire:loading wire:target="save" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </span>

                                        {{-- Icon when idle --}}
                                        <span wire:loading.remove wire:target="save">
                                            @if ($isEditing)
                                                <i class="ri ri-save-line"></i>
                                            @else
                                                <i class="ri ri-add-circle-line"></i>
                                            @endif
                                        </span>

                                        <span wire:loading.remove wire:target="save">
                                            {{ $isEditing ? 'Update Organisation' : 'Create Organisation' }}
                                        </span>
                                        <span wire:loading wire:target="save" class="d-none">
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
        <div id="formToast" x-data="{
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
        }" x-show="show" x-transition
            class="toast align-items-center border-0 shadow-lg"
            :class="type === 'success' ? 'bg-success' : 'bg-danger'" role="alert" aria-live="assertive"
            aria-atomic="true" style="display: none;">

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
                <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="show = false"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Custom CSS for Bootstrap enhancements --}}
    <style>
        :root {
            --indigo: #667eea;
            --indigo-light: #eef2f9;
        }

        .text-indigo {
            color: var(--indigo) !important;
        }

        .bg-indigo-light {
            background-color: var(--indigo-light);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--indigo);
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

        .btn-group-lg>.btn {
            padding: 0.5rem 1rem;
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .letter-spacing-1 {
            letter-spacing: 0.1em;
        }

        .alert-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
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

        /* Status button groups */
        .btn-group .btn-check:checked+.btn {
            font-weight: 600;
        }

        /* Smooth transitions */
        .btn,
        .form-control,
        .form-select {
            transition: all 0.2s ease;
        }
    </style>
</div>
