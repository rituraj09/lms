<div class="min-h-screen bg-light py-4 py-lg-5">
    <div class="container-fluid px-4">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}" class="text-decoration-none">
                                <i class="ri ri-dashboard-line me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="ri ri-file-chart-line me-1"></i>Reports
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $view === 0 ? 'Student Report' : 'Student Details' }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- ════════════════════════════════════════════ --}}
        {{-- VIEW 0: DATATABLE                           --}}
        {{-- ════════════════════════════════════════════ --}}
        @if ($view === 0)
            <div class="card shadow-sm border-0">
                <livewire:datatable model="App\Models\User" title="Student list" :new-entry="false" :getPrintData="true"
                    :columns="[
                        ['key' => 'details.student_id', 'label' => 'Student ID', 'searchable' => true],
                        ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
                        ['key' => 'email', 'label' => 'Email', 'searchable' => true],
                        ['key' => 'phone', 'label' => 'Phone', 'searchable' => true],
                        ['key' => 'organisation.name', 'label' => 'Organisation', 'searchable' => true],
                        [
                            'key' => 'promotion_level',
                            'label' => 'Promotion Level',
                            'searchable' => false,
                            'sortable' => false,
                        ],
                        ['key' => 'status', 'label' => 'Status', 'searchable' => true],
                        ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
                    ]" :actions="[
                        [
                            'label' => 'View',
                            'icon' => 'icon-base ri ri-eye-line',
                            'event' => 'edit',
                            'class' => 'btn-outline-primary',
                        ],
                        [
                            'label' => 'Reports',
                            'icon' => 'icon-base ri ri-bar-chart-line',
                            'event' => 'report_card',
                            'class' => 'btn-primary',
                        ],
                    ]" />
            </div>

            {{-- ════════════════════════════════════════════ --}}
            {{-- VIEW 1: USER DETAILS                        --}}
            {{-- ════════════════════════════════════════════ --}}
        @elseif($view === 1 && $selectedUser)
            @php
                $promotionMap = $selectedUser->promotionMap ?? [];
                $assessmentConfig = [
                    'iq' => ['primary', 'IQ', 'ri-brain-line'],
                    'eq' => ['success', 'EQ', 'ri-heart-line'],
                    'lq' => ['warning', 'LQ', 'ri-lightbulb-line'],
                ];
            @endphp

            <div class="row">
                {{-- Back Button --}}
                <div class="col-12 mb-3">
                    <button wire:click="backToList" class="btn btn-outline-secondary btn-sm">
                        <i class="ri ri-arrow-left-line me-1"></i> Back to List
                    </button>
                </div>

                {{-- Header Card --}}
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex align-items-start gap-4">
                                {{-- Avatar --}}
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:80px; height:80px; font-size:32px; font-weight:bold;">
                                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                                </div>

                                {{-- User Info --}}
                                <div class="flex-grow-1">
                                    <h4 class="mb-1 fw-bold">{{ $selectedUser->name }}</h4>
                                    <p class="text-muted mb-2">
                                        <i class="ri ri-mail-line me-1"></i>{{ $selectedUser->email }}
                                    </p>

                                    {{-- Promotion Levels --}}
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach ($assessmentConfig as $type => [$badgeColor, $typeLabel, $typeIcon])
                                            @php
                                                $promotionName = $promotionMap[$type] ?? null;
                                            @endphp

                                            <span
                                                class="badge bg-{{ $badgeColor }} bg-opacity-15 text-white d-inline-flex align-items-center gap-1 px-3 py-2"
                                                style="font-size: 0.75rem; font-weight: 500;"
                                                title="{{ $promotionName ? $typeLabel . ': ' . $promotionName : $typeLabel . ': Not assigned' }}">
                                                <i class="ri {{ $typeIcon }}" style="font-size: 0.85rem;"></i>
                                                <span>{{ $typeLabel }}</span>
                                                @if ($promotionName)
                                                    <span class="mx-1">·</span>
                                                    <span>{{ Str::limit($promotionName, 15) }}</span>
                                                @else
                                                    <span class="ms-1 opacity-50">—</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>

                                    {{-- Badges --}}
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($selectedUser->details?->student_id)
                                            <span class="badge bg-info text-white">
                                                <i
                                                    class="ri ri-id-card-line me-1"></i>{{ $selectedUser->details->student_id }}
                                            </span>
                                        @endif
                                        <span
                                            class="badge {{ match ($selectedUser->status) {
                                                'active' => 'bg-success',
                                                'inactive' => 'bg-danger',
                                                'pending' => 'bg-warning',
                                                'suspended' => 'bg-dark',
                                                default => 'bg-secondary',
                                            } }}">
                                            {{ ucfirst($selectedUser->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="d-flex gap-2">
                                    <a href="mailto:{{ $selectedUser->email }}" class="btn btn-outline-primary btn-sm"
                                        title="Send Email">
                                        <i class="ri ri-mail-line"></i>
                                    </a>
                                    @if ($selectedUser->phone)
                                        <a href="tel:{{ $selectedUser->phone }}" class="btn btn-outline-primary btn-sm"
                                            title="Call">
                                            <i class="ri ri-phone-line"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light px-4 py-3 border-0">
                            <h6 class="mb-0 fw-bold text-uppercase" style="font-size:12px; letter-spacing:.5px;">
                                <i class="ri ri-user-line me-2 text-primary"></i>Personal Information
                            </h6>
                        </div>
                        <div class="card-body px-4 py-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Full Name',
                                        'value' => $selectedUser->name,
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Email Address',
                                        'value' => $selectedUser->email,
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Phone Number',
                                        'value' => $selectedUser->phone ?? '—',
                                    ])
                                </div>
                                @if ($selectedUser->details)
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'First Name',
                                            'value' => $selectedUser->details->first_name ?? '—',
                                        ])
                                    </div>
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Last Name',
                                            'value' => $selectedUser->details->last_name ?? '—',
                                        ])
                                    </div>
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Date of Birth',
                                            'value' => $selectedUser->details->date_of_birth
                                                ? \Carbon\Carbon::parse(
                                                    $selectedUser->details->date_of_birth)->format('d M Y')
                                                : '—',
                                        ])
                                    </div>
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Gender',
                                            'value' => $selectedUser->details->gender
                                                ? ucfirst($selectedUser->details->gender)
                                                : '—',
                                        ])
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact & Address Information --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light px-4 py-3 border-0">
                            <h6 class="mb-0 fw-bold text-uppercase" style="font-size:12px; letter-spacing:.5px;">
                                <i class="ri ri-map-pin-line me-2 text-primary"></i>Address & Contact
                            </h6>
                        </div>
                        <div class="card-body px-4 py-4">
                            <div class="row g-3">
                                @if ($selectedUser->details?->address_line1)
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Address Line 1',
                                            'value' => $selectedUser->details->address_line1,
                                        ])
                                    </div>
                                @endif
                                @if ($selectedUser->details?->address_line2)
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Address Line 2',
                                            'value' => $selectedUser->details->address_line2,
                                        ])
                                    </div>
                                @endif
                                <div class="col-sm-6">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'City',
                                        'value' => $selectedUser->details?->city ?? '—',
                                    ])
                                </div>
                                <div class="col-sm-6">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'State',
                                        'value' => $selectedUser->details?->state->name ?? '—',
                                    ])
                                </div>
                                <div class="col-sm-6">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Country',
                                        'value' => $selectedUser->details?->country ?? '—',
                                    ])
                                </div>
                                <div class="col-sm-6">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Postal Code',
                                        'value' => $selectedUser->details?->postal_code ?? '—',
                                    ])
                                </div>
                                @if ($selectedUser->details?->emergency_contact_name)
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Emergency Contact Name',
                                            'value' => $selectedUser->details->emergency_contact_name,
                                        ])
                                    </div>
                                @endif
                                @if ($selectedUser->details?->emergency_contact_phone)
                                    <div class="col-12">
                                        @include('livewire.partials.info-field', [
                                            'label' => 'Emergency Contact Phone',
                                            'value' => $selectedUser->details->emergency_contact_phone,
                                        ])
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Information --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light px-4 py-3 border-0">
                            <h6 class="mb-0 fw-bold text-uppercase" style="font-size:12px; letter-spacing:.5px;">
                                <i class="ri ri-settings-3-line me-2 text-primary"></i>Account Information
                            </h6>
                        </div>
                        <div class="card-body px-4 py-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Student ID',
                                        'value' => $selectedUser->details?->student_id ?? '—',
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Organisation',
                                        'value' => $selectedUser->organisation?->name ?? '—',
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Status',
                                        'value' => ucfirst($selectedUser->status),
                                        'badge' => true,
                                        'badgeClass' => match ($selectedUser->status) {
                                            'active' => 'bg-success',
                                            'inactive' => 'bg-danger',
                                            'pending' => 'bg-warning text-dark',
                                            'suspended' => 'bg-dark',
                                            default => 'bg-secondary',
                                        },
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Enrolled On',
                                        'value' => $selectedUser->created_at->format('d M Y, h:i A'),
                                    ])
                                </div>
                                <div class="col-12">
                                    @include('livewire.partials.info-field', [
                                        'label' => 'Last Updated',
                                        'value' => $selectedUser->updated_at->format('d M Y, h:i A'),
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bio / Additional Info --}}
                @if ($selectedUser->details?->bio)
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light px-4 py-3 border-0">
                                <h6 class="mb-0 fw-bold text-uppercase" style="font-size:12px; letter-spacing:.5px;">
                                    <i class="ri ri-file-text-line me-2 text-primary"></i>Biography
                                </h6>
                            </div>
                            <div class="card-body px-4 py-4">
                                <p class="text-muted mb-0" style="line-height: 1.6;">
                                    {{ $selectedUser->details->bio }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
