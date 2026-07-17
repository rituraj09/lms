{{-- resources/views/livewire/admin/reports/student-report-card.blade.php --}}
<div>

    {{-- ── Print Styles (INLINE — NOT pushed, so it always renders) ─── --}}
    <style>
        /* Print-only elements hidden on screen by default */
        .d-print-block {
            display: none;
        }

        @media print {

            @page {
                size: A4;
                margin: 1.2cm;
            }

            html,
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* Hide EVERYTHING in the page (this also hides any parent layout topbar/sidebar) */
            body * {
                visibility: hidden !important;
            }

            /* Reveal only the printable card and its children */
            #printable-area,
            #printable-area * {
                visibility: visible !important;
            }

            #printable-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Belt-and-braces: explicitly nuke any interactive / nav elements
               even if they happen to be inside #printable-area */
            .no-print,
            .btn,
            a.btn,
            button,
            .nav-tabs,
            .breadcrumb,
            nav,
            .navbar,
            .sidebar,
            #sidebar,
            .topbar,
            .app-header {
                display: none !important;
                visibility: hidden !important;
            }

            /* Force-show print-only blocks */
            .d-print-block {
                display: block !important;
                visibility: visible !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-inside: avoid;
            }

            .card-body,
            .card-header,
            .card-footer {
                background: #fff !important;
            }

            .report-header {
                background: #fff !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
            }

            .report-header-sub {
                opacity: 1 !important;
                color: #333 !important;
            }

            .report-avatar-wrap {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }

            .report-mini-card {
                border: 1px solid #999 !important;
                box-shadow: none !important;
            }

            .report-promo-card {
                border: 1px solid #999 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }

            .promo-color-header {
                background: #f0f0f0 !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
            }

            .promo-color-body {
                background: #fff !important;
            }

            .progress {
                border: 1px solid #000 !important;
                background: #fff !important;
            }

            .progress-bar {
                background: #555 !important;
                color: #fff !important;
            }

            .badge {
                border: 1px solid #000 !important;
                background: #fff !important;
                color: #000 !important;
                font-weight: 600;
            }

            .alert {
                border: 1px solid #000 !important;
                background: #fff !important;
                color: #000 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table th,
            table td {
                border: 1px solid #000 !important;
                background: #fff !important;
            }

            tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>

    {{-- ── Page Header (never printed) ─────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ri ri-bar-chart-line me-2 text-primary"></i>
                Student Report Card
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ $backUrl }}">Student List</a>
                    </li>
                    <li class="breadcrumb-item active">Report Card</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="ri ri-arrow-left-line"></i>
                Back
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="ri ri-printer-line me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- ── Student Info Card ────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-4" id="printable-area">

        {{-- PRINT-ONLY LETTERHEAD --}}
        <div class="d-print-block text-center px-4 pt-4">
            <h3 class="fw-bold mb-0 text-uppercase">
                {{ $student->organisation?->name ?? 'Institution Name' }}
            </h3>
            <p class="mb-1 text-uppercase" style="letter-spacing:2px;">Student Report Card</p>
            <hr class="my-2" style="border-top:2px solid #000;">
            <p class="small mb-3">
                Generated on {{ now()->format('d M Y, h:i A') }}
            </p>
        </div>

        {{-- Header --}}
        <div class="card-header report-header p-4 text-white"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white d-flex align-items-center
                            justify-content-center shadow report-avatar-wrap"
                    style="width:64px;height:64px;min-width:64px;">
                    <img src="{{ $student->avatar_url }}" alt="avatar"
                        class="rounded-circle w-100 h-100 object-fit-cover">
                </div>
                <div>
                    <h5 class="mb-1 fw-bold">{{ $student->full_name }}</h5>
                    <p class="mb-0 opacity-75 small report-header-sub">
                        {{ $student->details?->student_id ?? 'N/A' }}
                        &bull;
                        {{ $student->email }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="card-body border-bottom bg-light p-4 report-info-section">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 report-mini-card">
                        <div class="card-body">
                            <p class="text-muted small mb-1">
                                <i class="ri ri-user-line me-1"></i> Student Name
                            </p>
                            <p class="fw-semibold mb-0">{{ $student->full_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 report-mini-card">
                        <div class="card-body">
                            <p class="text-muted small mb-1">
                                <i class="ri ri-building-line me-1"></i> Organisation
                            </p>
                            <p class="fw-semibold mb-0">
                                {{ $student->organisation?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 report-mini-card">
                        <div class="card-body">
                            <p class="text-muted small mb-1">
                                <i class="ri ri-cake-line me-1"></i> Physical Age
                            </p>
                            <p class="fw-semibold mb-0">{{ $physicalAge ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 report-mini-card">
                        <div class="card-body">
                            <p class="text-muted small mb-1">
                                <i class="ri ri-phone-line me-1"></i> Phone
                            </p>
                            <p class="fw-semibold mb-0">{{ $student->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Promotion Cards --}}
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="ri ri-award-line me-2 text-primary"></i>
                Current Promotion Levels
            </h5>
            <div class="row g-4">
                @foreach ($assessmentTypes as $typeKey => $typeLabel)
                    @php
                        $current = $currentPromotions[$typeKey] ?? null;
                        $promotion = $current?->promotionDetail?->currentPromotion;
                        $nextPromotion = $current?->promotionDetail?->nextPromotion;
                        $attempt = $current?->testAttempt;
                        $assessment = $attempt?->assessment;
                        $percentage = $this->getScorePercentage($attempt, $assessment);
                        $ageGroup = $promotion?->ageGroup?->name ?? '—';

                        $colors = [
                            'iq' => ['bg' => '#4e73df', 'light' => '#eef2ff'],
                            'eq' => ['bg' => '#1cc88a', 'light' => '#e6fff6'],
                            'lq' => ['bg' => '#f6c23e', 'light' => '#fffbee'],
                        ];
                        $color = $colors[$typeKey];

                        $progressClass =
                            $percentage >= 70 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger');
                    @endphp

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden report-promo-card">

                            <div class="p-3 text-white d-flex align-items-center
                                        justify-content-between promo-color-header"
                                style="background:{{ $color['bg'] }};">
                                <span class="fw-bold fs-5">{{ $typeLabel }}</span>
                                @if ($promotion?->badge)
                                    <img src="{{ asset('storage/' . $promotion->badge) }}" alt="badge"
                                        style="height:40px;width:40px;object-fit:contain;">
                                @else
                                    <i class="ri ri-medal-line fs-3 opacity-75"></i>
                                @endif
                            </div>

                            <div class="card-body promo-color-body" style="background:{{ $color['light'] }};">
                                @if ($current && $promotion)
                                    <p class="fw-bold fs-6 mb-1">{{ $promotion->name }}</p>
                                    <p class="text-muted small mb-3">
                                        <i class="ri ri-group-line me-1"></i>
                                        Age Group: <strong>{{ $ageGroup }}</strong>
                                    </p>

                                    @if ($attempt)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="text-muted">Score</span>
                                                <span class="fw-semibold">
                                                    {{ $attempt->total_score }}
                                                    / {{ $assessment?->total_marks ?? '?' }}
                                                    ({{ $percentage }}%)
                                                </span>
                                            </div>
                                            <div class="progress" style="height:8px;">
                                                <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                                    style="width:{{ $percentage }}%;"
                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2">
                                            <i class="ri ri-calendar-check-line me-1"></i>
                                            Completed:
                                            {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, h:i A') : 'N/A' }}
                                        </p>
                                        <p class="small text-muted mb-2">
                                            <i class="ri ri-hashtag me-1"></i>
                                            Ack No: {{ $attempt->ack_no }}
                                        </p>
                                    @else
                                        <p class="small text-muted mb-2">
                                            No test attempt linked.
                                        </p>
                                    @endif

                                    <div class="d-flex justify-content-between small text-muted mb-3">
                                        <span>
                                            Range:
                                            {{ $current->promotionDetail->percentage_min }}%
                                            – {{ $current->promotionDetail->percentage_max }}%
                                        </span>
                                    </div>

                                    @if ($nextPromotion)
                                        <div class="alert alert-info py-2 px-3 mb-3 small border-0 rounded-3">
                                            <i class="ri ri-arrow-up-circle-line me-1"></i>
                                            Next: <strong>{{ $nextPromotion->name }}</strong>
                                        </div>
                                    @else
                                        <div class="alert alert-success py-2 px-3 mb-3 small border-0 rounded-3">
                                            <i class="ri ri-trophy-line me-1"></i>
                                            Highest Promotion Reached!
                                        </div>
                                    @endif

                                    @if (($promotionHistories[$typeKey] ?? collect())->count() > 1)
                                        <button wire:click="openHistoryModal('{{ $typeKey }}')"
                                            class="btn btn-sm btn-outline-secondary w-100 no-print">
                                            <i class="ri ri-history-line me-1"></i>
                                            View History
                                            ({{ ($promotionHistories[$typeKey] ?? collect())->count() }})
                                        </button>
                                    @endif
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="ri ri-inbox-line fs-2 d-block mb-2"></i>
                                        <span class="small">No promotion assigned yet.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- All Test Attempts --}}
        <div class="card-body border-top p-4">
            <h5 class="fw-bold mb-4">
                <i class="ri ri-file-list-3-line me-2 text-primary"></i>
                All Test Attempts
            </h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase fw-bold">#</th>
                            <th class="small text-uppercase fw-bold">Ack No</th>
                            <th class="small text-uppercase fw-bold">Type</th>
                            <th class="small text-uppercase fw-bold">Level</th>
                            <th class="small text-uppercase fw-bold">Assessment</th>
                            <th class="small text-uppercase fw-bold">Score</th>
                            <th class="small text-uppercase fw-bold">Percentage</th>
                            <th class="small text-uppercase fw-bold">Status</th>
                            <th class="small text-uppercase fw-bold">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allAttempts = $student->testAttempts()->with('assessment')->latest()->get();
                        @endphp
                        @forelse ($allAttempts as $index => $attempt)
                            @php
                                $pct = 0;
                                if ($attempt->assessment?->total_marks > 0) {
                                    $pct = round(($attempt->total_score / $attempt->assessment->total_marks) * 100, 1);
                                }
                                $badgeClass = match ($attempt->status) {
                                    'submitted' => 'bg-success',
                                    'evaluated' => 'bg-primary',
                                    'in_progress' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };
                                $pctClass = $pct >= 70 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger');
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ $index + 1 }}</td>
                                <td><span class="font-monospace small">{{ $attempt->ack_no }}</span></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ strtoupper($attempt->assessment?->assessment_type_id ?? '—') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ strtoupper($attempt->assessment?->ageGroup?->name ?? '—') }}
                                        | Level-{{ strtoupper($attempt->assessment?->difficultyLevel?->level ?? '—') }}
                                    </span>
                                </td>
                                <td>{{ $attempt->assessment?->title ?? '—' }}</td>
                                <td>
                                    {{ $attempt->total_score }}
                                    / {{ $attempt->assessment?->total_marks ?? '?' }}
                                    @if ($attempt->negative_score > 0)
                                        <br><small class="text-danger">-{{ $attempt->negative_score }}
                                            (negative)
                                        </small>
                                    @endif
                                </td>
                                <td class="{{ $pctClass }} fw-semibold">{{ $pct }}%</td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                    </span>
                                </td>
                                <td class="small">
                                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, h:i A') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ri ri-inbox-line fs-3 d-block mb-2"></i>
                                    No test attempts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PRINT-ONLY SIGNATURE FOOTER --}}
        <div class="d-print-block px-4 pb-4 pt-3">
            <div class="row mt-5 pt-5">
                <div class="col-4 text-center">
                    <div style="border-top:1px solid #000;"></div>
                    <p class="small mt-1 mb-0">Class Teacher</p>
                </div>
                <div class="col-4 text-center">
                    <div style="border-top:1px solid #000;"></div>
                    <p class="small mt-1 mb-0">Principal</p>
                </div>
                <div class="col-4 text-center">
                    <div style="border-top:1px solid #000;"></div>
                    <p class="small mt-1 mb-0">Parent / Guardian</p>
                </div>
            </div>
            <p class="text-center small text-muted mt-4 mb-0">
                This is a system-generated report card. Ack No / details above serve as verification reference.
            </p>
        </div>

        {{-- Footer (screen only) --}}
        <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="ri ri-printer-line me-1"></i> Print Report Card
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     PROMOTION HISTORY MODAL — Professional Redesign
     ════════════════════════════════════════════════════════════ --}}
    @if ($showModal)

        {{-- Backdrop --}}
        <div wire:click="closeModal"
            style="
            position:fixed;inset:0;
            background:rgba(15,23,42,.55);
            backdrop-filter:blur(4px);
            -webkit-backdrop-filter:blur(4px);
            z-index:99998;
            animation:fadeIn .2s ease;
         ">
        </div>

        {{-- Modal Panel --}}
        <div
            style="
            position:fixed;
            top:50%;left:50%;
            transform:translate(-50%,-50%);
            z-index:99999;
            width:min(1000px,96vw);
            max-height:92vh;
            display:flex;
            flex-direction:column;
            background:#fff;
            border-radius:16px;
            box-shadow:0 25px 60px rgba(0,0,0,.25), 0 0 0 1px rgba(0,0,0,.06);
            animation:slideUp .25s ease;
            overflow:hidden;
         ">

            {{-- ── Modal Header ───────────────────────────────────── --}}
            <div
                style="
                background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
                padding:24px 28px 20px;
                flex-shrink:0;
             ">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div
                                style="
                                width:36px;height:36px;
                                background:rgba(255,255,255,.2);
                                border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                             ">
                                <i class="ri ri-history-line text-white fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-white">Promotion History</h5>
                        </div>
                        <p class="mb-0 small" style="color:rgba(255,255,255,.7);padding-left:44px;">
                            {{ $student->full_name }}
                            &bull;
                            {{ $student->details?->student_id ?? 'N/A' }}
                        </p>
                    </div>
                    <button wire:click="closeModal"
                        style="
                            width:36px;height:36px;
                            background:rgba(255,255,255,.15);
                            border:1px solid rgba(255,255,255,.25);
                            border-radius:8px;
                            color:#fff;
                            font-size:18px;
                            display:flex;align-items:center;justify-content:center;
                            cursor:pointer;
                            transition:background .15s;
                            flex-shrink:0;
                        "
                        onmouseover="this.style.background='rgba(255,255,255,.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,.15)'">
                        &times;
                    </button>
                </div>

                {{-- Type Tabs inside header --}}
                <div class="d-flex gap-2 mt-4">
                    @foreach ($assessmentTypes as $typeKey => $typeLabel)
                        @php
                            $tabCount = ($promotionHistories[$typeKey] ?? collect())->count();
                            $isActive = $activeHistoryType === $typeKey;

                            $tabColors = [
                                'iq' => '#4e73df',
                                'eq' => '#1cc88a',
                                'lq' => '#f6c23e',
                            ];
                            $tabColor = $tabColors[$typeKey] ?? '#6c757d';
                        @endphp
                        <button wire:click="setActiveHistoryType('{{ $typeKey }}')"
                            style="
                                padding:8px 18px;
                                border-radius:30px;
                                border:2px solid {{ $isActive ? '#fff' : 'rgba(255,255,255,.35)' }};
                                background:{{ $isActive ? '#fff' : 'transparent' }};
                                color:{{ $isActive ? '#764ba2' : 'rgba(255,255,255,.85)' }};
                                font-weight:{{ $isActive ? '700' : '500' }};
                                font-size:13px;
                                cursor:pointer;
                                display:inline-flex;
                                align-items:center;
                                gap:8px;
                                transition:all .15s;
                            ">
                            {{ $typeLabel }}
                            @if ($tabCount)
                                <span
                                    style="
                                    background:{{ $isActive ? $tabColor : 'rgba(255,255,255,.25)' }};
                                    color:{{ $isActive ? '#fff' : 'rgba(255,255,255,.9)' }};
                                    border-radius:20px;
                                    padding:1px 8px;
                                    font-size:11px;
                                    font-weight:700;
                                  ">
                                    {{ $tabCount }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ── Summary Stats Bar ───────────────────────────────── --}}
            @php
                $activeHistory = $promotionHistories[$activeHistoryType] ?? collect();
                $currentRecord = $activeHistory->firstWhere('current_status', true);
                $totalAttempts = $activeHistory->count();
                $bestScore = 0;
                $avgScore = 0;

                foreach ($activeHistory as $rec) {
                    $att = $rec->testAttempt;
                    if ($att && ($att->assessment?->total_marks ?? 0) > 0) {
                        $pctVal = round(($att->total_score / $att->assessment->total_marks) * 100, 1);
                        if ($pctVal > $bestScore) {
                            $bestScore = $pctVal;
                        }
                        $avgScore += $pctVal;
                    }
                }
                $avgScore = $totalAttempts > 0 ? round($avgScore / $totalAttempts, 1) : 0;

                $currentPromo = $currentRecord?->promotionDetail?->currentPromotion;
            @endphp

            <div
                style="
                background:#f8fafc;
                border-bottom:1px solid #e9ecef;
                padding:16px 28px;
                flex-shrink:0;
             ">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                width:40px;height:40px;
                                background:#ede9fe;
                                border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                                flex-shrink:0;
                             ">
                                <i class="ri ri-bar-chart-line" style="color:#7c3aed;font-size:18px;"></i>
                            </div>
                            <div>
                                <div
                                    style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">
                                    Total Attempts
                                </div>
                                <div style="font-size:20px;font-weight:700;color:#1e293b;line-height:1.2;">
                                    {{ $totalAttempts }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                width:40px;height:40px;
                                background:#dcfce7;
                                border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                                flex-shrink:0;
                             ">
                                <i class="ri ri-trophy-line" style="color:#16a34a;font-size:18px;"></i>
                            </div>
                            <div>
                                <div
                                    style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">
                                    Best Score
                                </div>
                                <div style="font-size:20px;font-weight:700;color:#1e293b;line-height:1.2;">
                                    {{ $bestScore }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                width:40px;height:40px;
                                background:#fef9c3;
                                border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                                flex-shrink:0;
                             ">
                                <i class="ri ri-percent-line" style="color:#ca8a04;font-size:18px;"></i>
                            </div>
                            <div>
                                <div
                                    style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">
                                    Avg Score
                                </div>
                                <div style="font-size:20px;font-weight:700;color:#1e293b;line-height:1.2;">
                                    {{ $avgScore }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                width:40px;height:40px;
                                background:#fee2e2;
                                border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                                flex-shrink:0;
                             ">
                                <i class="ri ri-award-line" style="color:#dc2626;font-size:18px;"></i>
                            </div>
                            <div>
                                <div
                                    style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">
                                    Current Level
                                </div>
                                <div style="font-size:14px;font-weight:700;color:#1e293b;line-height:1.2;"
                                    title="{{ $currentPromo?->name ?? 'N/A' }}">
                                    {{ Str::limit($currentPromo?->name ?? 'N/A', 16) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Modal Body — History Table ──────────────────────── --}}
            <div style="overflow-y:auto;flex:1;padding:24px 28px;">

                @if ($activeHistory->isEmpty())
                    <div class="text-center py-5">
                        <div
                            style="
                            width:64px;height:64px;
                            background:#f1f5f9;
                            border-radius:50%;
                            display:flex;align-items:center;justify-content:center;
                            margin:0 auto 16px;
                         ">
                            <i class="ri ri-inbox-line" style="font-size:28px;color:#94a3b8;"></i>
                        </div>
                        <h6 class="fw-semibold text-muted mb-1">No History Found</h6>
                        <p class="small text-muted mb-0">
                            No promotion records for {{ strtoupper($activeHistoryType) }} yet.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table
                            style="
                                width:100%;
                                border-collapse:separate;
                                border-spacing:0 6px;
                                font-size:13px;
                           ">
                            <thead>
                                <tr>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                        width:36px;
                                   ">
                                        #</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Promotion Level</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Age Group</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Score Range</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Test Score</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Next Level</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Status</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Date</th>
                                    <th
                                        style="
                                        padding:10px 14px;
                                        background:#f8fafc;
                                        border-top:1px solid #e2e8f0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-size:10px;
                                        text-transform:uppercase;
                                        letter-spacing:.8px;
                                        color:#64748b;
                                        font-weight:700;
                                        white-space:nowrap;
                                   ">
                                        Certificate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeHistory as $index => $record)
                                    @php
                                        $promo = $record->promotionDetail?->currentPromotion;
                                        $nextP = $record->promotionDetail?->nextPromotion;
                                        $att = $record->testAttempt;
                                        $isCurrent = (bool) $record->current_status;
                                        $pct = 0;

                                        if ($att && ($att->assessment?->total_marks ?? 0) > 0) {
                                            $pct = round(($att->total_score / $att->assessment->total_marks) * 100, 1);
                                        }

                                        $pctColor = $pct >= 70 ? '#16a34a' : ($pct >= 50 ? '#ca8a04' : '#dc2626');
                                        $pctBg = $pct >= 70 ? '#dcfce7' : ($pct >= 50 ? '#fef9c3' : '#fee2e2');

                                        $rowBg = $isCurrent ? '#fdf8ff' : '#fff';
                                        $rowBorder = $isCurrent ? '#e9d5ff' : '#f1f5f9';
                                    @endphp
                                    <tr
                                        style="
                                        background:{{ $rowBg }};
                                        border-radius:10px;
                                   ">
                                        {{-- # --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            border-left:1px solid {{ $rowBorder }};
                                            border-radius:10px 0 0 10px;
                                            {{ $isCurrent ? 'border-left:3px solid #7c3aed;' : '' }}
                                            color:#94a3b8;
                                            font-weight:600;
                                            font-size:12px;
                                       ">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- Promotion Level --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($promo?->badge)
                                                    <img src="{{ asset('storage/' . $promo->badge) }}" alt="badge"
                                                        style="height:32px;width:32px;object-fit:contain;
                                                            border-radius:6px;background:#f8fafc;
                                                            padding:2px;border:1px solid #e2e8f0;">
                                                @else
                                                    <div
                                                        style="
                                                        width:32px;height:32px;
                                                        background:#ede9fe;
                                                        border-radius:8px;
                                                        display:flex;align-items:center;justify-content:center;
                                                     ">
                                                        <i class="ri ri-award-line"
                                                            style="color:#7c3aed;font-size:16px;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div style="font-weight:600;color:#1e293b;font-size:13px;">
                                                        {{ $promo?->name ?? '—' }}
                                                    </div>
                                                    @if ($isCurrent)
                                                        <div
                                                            style="font-size:10px;color:#7c3aed;font-weight:600;
                                                                text-transform:uppercase;letter-spacing:.5px;">
                                                            ● Active
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Age Group --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            <span
                                                style="
                                                background:#f1f5f9;
                                                color:#475569;
                                                padding:4px 10px;
                                                border-radius:20px;
                                                font-size:12px;
                                                font-weight:600;
                                              ">
                                                {{ $promo?->ageGroup?->name ?? '—' }}
                                            </span>
                                        </td>

                                        {{-- Score Range --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            <div style="font-size:12px;color:#64748b;">
                                                <span style="font-weight:600;color:#475569;">
                                                    {{ $record->promotionDetail?->percentage_min ?? '?' }}%
                                                </span>
                                                <span style="color:#94a3b8;margin:0 4px;">to</span>
                                                <span style="font-weight:600;color:#475569;">
                                                    {{ $record->promotionDetail?->percentage_max ?? '?' }}%
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Test Score --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            @if ($att)
                                                <div style="font-weight:600;color:#1e293b;font-size:13px;">
                                                    {{ $att->total_score }}
                                                    <span style="color:#94a3b8;font-weight:400;">
                                                        / {{ $att->assessment?->total_marks ?? '?' }}
                                                    </span>
                                                </div>
                                                <div style="margin-top:5px;">
                                                    {{-- Mini progress bar --}}
                                                    <div
                                                        style="
                                                        width:90px;height:5px;
                                                        background:#e2e8f0;
                                                        border-radius:99px;
                                                        overflow:hidden;
                                                     ">
                                                        <div
                                                            style="
                                                            width:{{ $pct }}%;
                                                            height:100%;
                                                            background:{{ $pctColor }};
                                                            border-radius:99px;
                                                         ">
                                                        </div>
                                                    </div>
                                                    <span
                                                        style="
                                                        font-size:11px;
                                                        font-weight:700;
                                                        color:{{ $pctColor }};
                                                        background:{{ $pctBg }};
                                                        padding:2px 7px;
                                                        border-radius:20px;
                                                        margin-top:4px;
                                                        display:inline-block;
                                                     ">
                                                        {{ $pct }}%
                                                    </span>
                                                </div>
                                            @else
                                                <span style="color:#cbd5e1;font-size:18px;">—</span>
                                            @endif
                                        </td>

                                        {{-- Next Level --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            @if ($nextP)
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ri ri-arrow-up-circle-line"
                                                        style="color:#3b82f6;font-size:14px;"></i>
                                                    <span style="font-size:12px;font-weight:600;color:#3b82f6;">
                                                        {{ $nextP->name }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="ri ri-crown-line"
                                                        style="color:#f59e0b;font-size:14px;"></i>
                                                    <span style="font-size:12px;font-weight:600;color:#f59e0b;">
                                                        Top Level
                                                    </span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            @if ($isCurrent)
                                                <span
                                                    style="
                                                    background:#ede9fe;
                                                    color:#7c3aed;
                                                    padding:5px 12px;
                                                    border-radius:20px;
                                                    font-size:11px;
                                                    font-weight:700;
                                                    letter-spacing:.4px;
                                                    text-transform:uppercase;
                                                  ">
                                                    ● Current
                                                </span>
                                            @else
                                                <span
                                                    style="
                                                    background:#f1f5f9;
                                                    color:#94a3b8;
                                                    padding:5px 12px;
                                                    border-radius:20px;
                                                    font-size:11px;
                                                    font-weight:600;
                                                    text-transform:uppercase;
                                                    letter-spacing:.4px;
                                                  ">
                                                    Past
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Date --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            white-space:nowrap;
                                       ">
                                            <div style="font-size:12px;font-weight:600;color:#374151;">
                                                {{ $record->created_at?->format('d M Y') }}
                                            </div>
                                            <div style="font-size:11px;color:#94a3b8;">
                                                {{ $record->created_at?->format('h:i A') }}
                                            </div>
                                        </td>

                                        {{-- Certificate --}}
                                        <td
                                            style="
                                            padding:14px 14px;
                                            border-top:1px solid {{ $rowBorder }};
                                            border-bottom:1px solid {{ $rowBorder }};
                                            border-right:1px solid {{ $rowBorder }};
                                            border-radius:0 10px 10px 0;
                                            white-space:nowrap;
                                       ">
                                            @if ($att)
                                                <a href="{{ route('admin.reports.promotion-certificate', [
                                                    'student' => Crypt::encrypt($student->id),
                                                    'history' => Crypt::encrypt($record->id),
                                                ]) }}"
                                                    target="_blank"
                                                    style="
                                                    display:inline-flex;
                                                    align-items:center;
                                                    gap:5px;
                                                    padding:7px 14px;
                                                    background:linear-gradient(135deg,#f59e0b,#d97706);
                                                    color:#fff;
                                                    border-radius:8px;
                                                    font-size:12px;
                                                    font-weight:600;
                                                    text-decoration:none;
                                                    transition:opacity .15s;
                                               "
                                                    onmouseover="this.style.opacity='.85'"
                                                    onmouseout="this.style.opacity='1'">
                                                    <i class="ri ri-award-line"></i>
                                                    Certificate
                                                </a>
                                            @else
                                                <span style="color:#cbd5e1;font-size:18px;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ── Modal Footer ───────────────────────────────────────── --}}
            <div
                style="
                padding:16px 28px;
                border-top:1px solid #f1f5f9;
                background:#f8fafc;
                display:flex;
                justify-content:space-between;
                align-items:center;
                flex-shrink:0;
             ">
                <span style="font-size:12px;color:#94a3b8;">
                    Showing {{ $activeHistory->count() }}
                    {{ Str::plural('record', $activeHistory->count()) }}
                    for <strong style="color:#64748b;">{{ strtoupper($activeHistoryType) }}</strong>
                </span>
                <button wire:click="closeModal"
                    style="
                        padding:10px 24px;
                        background:#fff;
                        border:1px solid #e2e8f0;
                        border-radius:8px;
                        font-size:13px;
                        font-weight:600;
                        color:#64748b;
                        cursor:pointer;
                        transition:all .15s;
                    "
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    Close
                </button>
            </div>
        </div>

        {{-- Animations --}}
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translate(-50%, calc(-50% + 20px));
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }
        </style>

    @endif

</div>
