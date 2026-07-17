{{-- resources/views/livewire/admin/student/student-list.blade.php --}}
<div class="min-h-screen py-4 py-lg-5" style="background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 100%);">
    <div class="container-xl">

        {{-- Page Header --}}
        <div class="row mb-4 align-items-center">
            <div class="col-12 col-md-8">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.org.dashboard', ['organisationId' => Crypt::encrypt($organisationId)]) }}"
                                class="text-decoration-none text-primary fw-medium d-inline-flex align-items-center gap-1">
                                <i class="ri ri-dashboard-line"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-medium">Student List</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span
                        class="icon-box bg-primary bg-opacity-10 rounded-3 d-inline-flex align-items-center justify-content-center"
                        style="width:42px; height:42px;">
                        <i class="ri ri-group-line text-primary fs-5"></i>
                    </span>
                    Student Reports
                </h1>
                <p class="text-muted mb-0 ms-1" style="padding-left: 50px;">
                    View and analyze student performance and progress
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <button onclick="printStudentTable()"
                    class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3 fw-medium no-print">
                    <i class="ri ri-printer-line fs-6"></i>
                    Print Table
                </button>
            </div>
        </div>


        {{-- Filters & Search --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-7">
                        <label class="form-label small fw-semibold text-muted text-uppercase ls-1 mb-2">
                            Search Students
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 ps-3"
                                style="border-radius: 12px 0 0 12px;">
                                <i class="ri ri-search-line text-muted fs-5"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="form-control border-start-0 py-3" style="border-radius: 0 12px 12px 0;"
                                placeholder="Search by name, email, phone or student ID...">
                            @if ($search)
                                <button wire:click="$set('search', '')"
                                    class="btn btn-link text-muted position-absolute end-0 top-50 translate-middle-y pe-3"
                                    style="z-index: 5;">
                                    <i class="ri ri-close-circle-fill fs-5"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase ls-1 mb-2">
                            Per Page
                        </label>
                        <select wire:model.live="perPage" class="form-select py-3" style="border-radius: 12px;">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 text-md-end">
                        <label class="form-label small fw-semibold text-muted text-uppercase ls-1 mb-2 d-block">
                            &nbsp;
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                                {{ $students->total() }} result{{ $students->total() !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================== --}}
        {{-- PRINTABLE SECTION STARTS HERE --}}
        {{-- ============================== --}}
        <div id="printable-table">

            {{-- Print Header (only visible when printing) --}}
            <div class="print-header d-none">
                <div class="print-org-header">
                    <div class="print-title-section">
                        <h2 class="print-main-title">Student List Report</h2>
                        <p class="print-subtitle">Complete student enrollment records</p>
                    </div>
                    <div class="print-meta-section">
                        <table class="print-meta-table">
                            <tr>
                                <td class="print-meta-label">Generated:</td>
                                <td class="print-meta-value">{{ now()->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="print-meta-label">Total Students:</td>
                                <td class="print-meta-value">{{ $students->total() }}</td>
                            </tr>
                            @if ($search)
                                <tr>
                                    <td class="print-meta-label">Search Filter:</td>
                                    <td class="print-meta-value">"{{ $search }}"</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Students Table --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                {{-- Table Toolbar --}}
                <div class="card-header bg-white border-bottom px-4 py-3 no-print">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px; height:36px;">
                                <i class="ri ri-table-line text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">All Students</h6>
                                <span class="text-muted" style="font-size: 0.78rem;">
                                    Showing {{ $students->firstItem() ?? 0 }} –
                                    {{ $students->lastItem() ?? 0 }}
                                    of {{ $students->total() }} entries
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if ($search)
                                <span
                                    class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                                    <i class="ri ri-filter-line me-1"></i>
                                    Filtered: "{{ Str::limit($search, 20) }}"
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="students-table">
                            <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);">
                                <tr>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase; width: 50px;">
                                        #
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-fingerprint-line me-1"></i>Student ID
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-user-line me-1"></i>Student Name
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-bar-chart-line me-1"></i>Promotion Level
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-radio-button-line me-1"></i>Status
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-calendar-line me-1"></i>Enrolled On
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-semibold border-0 text-center no-print"
                                        style="font-size: 0.78rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                        <i class="ri ri-settings-3-line me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $promotionMap = $student->promotionMap ?? [];
                                        $assessmentConfig = [
                                            'iq' => ['4facfe', '00f2fe', 'IQ', 'ri-brain-line'],
                                            'eq' => ['11998e', '38ef7d', 'EQ', 'ri-heart-line'],
                                            'lq' => ['f093fb', 'f5576c', 'LQ', 'ri-lightbulb-line'],
                                        ];
                                        $statusConfig = [
                                            'active' => ['success', 'Active', 'ri-checkbox-circle-line'],
                                            'inactive' => ['secondary', 'Inactive', 'ri-close-circle-line'],
                                            'suspended' => ['danger', 'Suspended', 'ri-forbid-line'],
                                            'pending' => ['warning', 'Pending', 'ri-time-line'],
                                        ];
                                        [$color, $label, $icon] = $statusConfig[$student->status] ?? [
                                            'secondary',
                                            'Unknown',
                                            'ri-question-line',
                                        ];
                                    @endphp

                                    <tr wire:key="student-{{ $student->id }}" class="student-row border-bottom">

                                        {{-- Row Number --}}
                                        <td class="px-4 py-3">
                                            <span class="text-muted fw-medium" style="font-size: 0.85rem;">
                                                {{ $students->firstItem() + $loop->index }}
                                            </span>
                                        </td>

                                        {{-- Student ID --}}
                                        <td class="px-4 py-3">
                                            <a href="{{ route('admin.org.students.view', [
                                                'organisationId' => Crypt::encrypt($organisationId),
                                                'studentId' => Crypt::encrypt($student->id),
                                            ]) }}"
                                                class="text-decoration-none no-print">
                                                <span class="badge px-3 py-2 fw-bold rounded-pill"
                                                    style="background: linear-gradient(135deg, #667eea20, #764ba220);
                                                           color: #667eea; border: 1px solid #667eea30;
                                                           font-size: 0.78rem; letter-spacing: 0.03em;">
                                                    {{ $student->details->student_id ?? 'N/A' }}
                                                </span>
                                            </a>
                                            <span class="print-only fw-bold text-dark" style="font-size: 0.85rem;">
                                                {{ $student->details->student_id ?? 'N/A' }}
                                            </span>
                                        </td>

                                        {{-- Student Name --}}
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">

                                                <div>
                                                    <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                                        {{ $student->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Promotion Level --}}
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($assessmentConfig as $type => [$from, $to, $typeLabel, $typeIcon])
                                                    @php $promotionName = $promotionMap[$type] ?? null; @endphp
                                                    <span
                                                        class="promotion-badge no-print d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill"
                                                        style="background: linear-gradient(135deg, #{{ $from }}20, #{{ $to }}20);
                                                               border: 1px solid #{{ $from }}40;
                                                               color: #{{ $from }};
                                                               font-size: 0.7rem; font-weight: 600;"
                                                        title="{{ $promotionName ? $typeLabel . ': ' . $promotionName : $typeLabel . ': Not assigned' }}">
                                                        <i class="ri {{ $typeIcon }}"
                                                            style="font-size: 0.72rem;"></i>
                                                        <span>{{ $typeLabel }}</span>
                                                        @if ($promotionName)
                                                            <span class="opacity-50">·</span>
                                                            <span>{{ Str::limit($promotionName, 30) }}</span>
                                                        @else
                                                            <span class="opacity-40">—</span>
                                                        @endif
                                                    </span>
                                                    <span class="print-only"
                                                        style="font-size:0.75rem; margin-right:4px;">
                                                        {{ $typeLabel }}: {{ $promotionName ?? '—' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3">
                                            <span
                                                class="status-pill d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill no-print"
                                                style="font-size: 0.78rem; font-weight: 600;">
                                                @switch($student->status)
                                                    @case('active')
                                                        <span
                                                            style="display:inline-flex; align-items:center; gap:6px;
                                                                     background:#dcfce7; color:#166534;
                                                                     padding:5px 12px; border-radius:999px;
                                                                     font-size:0.78rem; font-weight:600;">
                                                            <span
                                                                style="width:7px; height:7px; border-radius:50%;
                                                                         background:#22c55e; display:inline-block;
                                                                         box-shadow: 0 0 0 2px #86efac;"></span>
                                                            Active
                                                        </span>
                                                    @break

                                                    @case('pending')
                                                        <span
                                                            style="display:inline-flex; align-items:center; gap:6px;
                                                                     background:#fef9c3; color:#854d0e;
                                                                     padding:5px 12px; border-radius:999px;
                                                                     font-size:0.78rem; font-weight:600;">
                                                            <span
                                                                style="width:7px; height:7px; border-radius:50%;
                                                                         background:#eab308; display:inline-block;
                                                                         box-shadow: 0 0 0 2px #fde047;"></span>
                                                            Pending
                                                        </span>
                                                    @break

                                                    @case('suspended')
                                                        <span
                                                            style="display:inline-flex; align-items:center; gap:6px;
                                                                     background:#fee2e2; color:#991b1b;
                                                                     padding:5px 12px; border-radius:999px;
                                                                     font-size:0.78rem; font-weight:600;">
                                                            <span
                                                                style="width:7px; height:7px; border-radius:50%;
                                                                         background:#ef4444; display:inline-block;
                                                                         box-shadow: 0 0 0 2px #fca5a5;"></span>
                                                            Suspended
                                                        </span>
                                                    @break

                                                    @default
                                                        <span
                                                            style="display:inline-flex; align-items:center; gap:6px;
                                                                     background:#f1f5f9; color:#475569;
                                                                     padding:5px 12px; border-radius:999px;
                                                                     font-size:0.78rem; font-weight:600;">
                                                            <span
                                                                style="width:7px; height:7px; border-radius:50%;
                                                                         background:#94a3b8; display:inline-block;"></span>
                                                            {{ ucfirst($student->status) }}
                                                        </span>
                                                @endswitch
                                            </span>
                                            {{-- Print-only plain text status --}}
                                            <span class="print-only fw-semibold" style="font-size:0.82rem;">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>

                                        {{-- Enrolled On --}}
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium text-dark" style="font-size: 0.85rem;">
                                                    {{ $student->created_at->format('d M Y') }}
                                                </span>
                                                <span class="text-muted no-print" style="font-size: 0.75rem;">
                                                    {{ $student->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-4 py-3 text-center no-print">
                                            <a href="{{ route('admin.org.reports.students-list.report-cards', [
                                                'organisationId' => Crypt::encrypt($organisationId),
                                                'id' => Crypt::encrypt($student->id),
                                            ]) }}"
                                                class="btn btn-sm px-3 py-2 fw-medium d-inline-flex align-items-center gap-2 rounded-3"
                                                style="background: linear-gradient(135deg, #667eea, #764ba2);
                                                       color: white; border: none; font-size: 0.78rem;
                                                       transition: all 0.2s ease;"
                                                title="View Details"
                                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.4)';"
                                                onmouseout="this.style.transform=''; this.style.boxShadow='';">
                                                <i class="ri ri-bar-chart-line"></i>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="py-4">
                                                    <div class="mb-3"
                                                        style="width:80px; height:80px; border-radius:50%;
                                                           background: linear-gradient(135deg, #f0f4ff, #e0e7ff);
                                                           display:flex; align-items:center; justify-content:center;
                                                           margin: 0 auto;">
                                                        <i class="ri ri-user-search-line text-primary"
                                                            style="font-size: 2.2rem;"></i>
                                                    </div>
                                                    <h6 class="text-dark fw-bold mb-1">No students found</h6>
                                                    <p class="text-muted mb-3" style="font-size: 0.88rem;">
                                                        @if ($search)
                                                            No results matched <strong>"{{ $search }}"</strong>
                                                        @else
                                                            No students have been enrolled yet.
                                                        @endif
                                                    </p>
                                                    @if ($search)
                                                        <button wire:click="$set('search', '')"
                                                            class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-medium">
                                                            <i class="ri ri-close-line me-1"></i>Clear Search
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

                    {{-- Footer: Pagination --}}
                    @if ($students->hasPages())
                        <div class="card-footer bg-white border-top px-4 py-3 no-print">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <span class="text-muted" style="font-size: 0.82rem;">
                                    Showing
                                    <strong class="text-dark">{{ $students->firstItem() }}</strong>
                                    to
                                    <strong class="text-dark">{{ $students->lastItem() }}</strong>
                                    of
                                    <strong class="text-dark">{{ $students->total() }}</strong>
                                    students
                                </span>
                                {{ $students->links() }}
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Print Footer --}}
                <div class="print-footer d-none">
                    <div
                        style="border-top: 2px solid #e5e7eb; margin-top: 24px; padding-top: 12px;
                             display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.75rem; color:#6b7280;">
                            Printed on {{ now()->format('d M Y \a\t h:i A') }}
                        </span>
                        <span style="font-size:0.75rem; color:#6b7280;">
                            Total Records: {{ $students->total() }}
                        </span>
                    </div>
                </div>

            </div>
            {{-- ============================== --}}
            {{-- PRINTABLE SECTION ENDS HERE   --}}
            {{-- ============================== --}}

        </div>

        {{-- ========================= --}}
        {{-- STYLES                   --}}
        {{-- ========================= --}}
        <style>
            /* ---------- Screen Styles ---------- */
            .student-row {
                transition: background-color 0.18s ease, transform 0.18s ease;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            .student-row:hover {
                background-color: #f8f9ff !important;
            }

            .student-row:last-child {
                border-bottom: none !important;
            }

            .ls-1 {
                letter-spacing: 0.06em;
            }

            /* print-only elements hidden on screen */
            .print-only {
                display: none !important;
            }

            /* ---------- Print Styles ---------- */
            @media print {

                /* Hide everything except the printable area */
                body * {
                    visibility: hidden;
                }

                #printable-table,
                #printable-table * {
                    visibility: visible;
                }

                #printable-table {
                    position: absolute;
                    inset: 0;
                    padding: 24px 32px;
                }

                /* Show print-only elements */
                .print-only {
                    display: inline !important;
                }

                /* Hide screen-only elements */
                .no-print,
                .no-print * {
                    display: none !important;
                }

                /* Show print header & footer */
                .print-header,
                .print-footer {
                    display: block !important;
                }

                /* Print header layout */
                .print-org-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 20px;
                    padding-bottom: 16px;
                    border-bottom: 3px solid #667eea;
                }

                .print-main-title {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #1e1b4b;
                    margin: 0 0 4px;
                }

                .print-subtitle {
                    font-size: 0.85rem;
                    color: #6b7280;
                    margin: 0;
                }

                .print-meta-table td {
                    padding: 2px 8px;
                    font-size: 0.8rem;
                }

                .print-meta-label {
                    color: #6b7280;
                    font-weight: 600;
                }

                .print-meta-value {
                    color: #111827;
                    font-weight: 500;
                }

                /* Table styles for print */
                #students-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 0.8rem;
                }

                #students-table thead tr {
                    background: #f0f4ff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                #students-table th {
                    padding: 10px 12px;
                    text-align: left;
                    font-weight: 700;
                    font-size: 0.72rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    color: #374151;
                    border-bottom: 2px solid #c7d2fe;
                }

                #students-table td {
                    padding: 9px 12px;
                    border-bottom: 1px solid #e5e7eb;
                    color: #111827;
                    vertical-align: middle;
                }

                #students-table tbody tr:nth-child(even) {
                    background: #fafbff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                #students-table tbody tr:hover {
                    background: transparent !important;
                }

                /* No page break inside rows */
                #students-table tbody tr {
                    page-break-inside: avoid;
                }

                @page {
                    margin: 18mm 14mm;
                    size: A4 landscape;
                }
            }
        </style>

        {{-- ========================= --}}
        {{-- SCRIPTS                  --}}
        {{-- ========================= --}}
        <script>
            function printStudentTable() {
                window.print();
            }
        </script>
    </div>
