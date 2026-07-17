<div>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #e9ecef;
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* ── Toolbar (screen only) ─────────────────────────────── */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 14px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .toolbar button,
        .toolbar a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-family: Arial, sans-serif;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-print {
            background: #4e73df;
            color: #fff;
        }

        .btn-close {
            background: #6c757d;
            color: #fff;
        }

        /* ── Certificate Page Wrapper ──────────────────────────── */
        .page-wrap {
            display: flex;
            justify-content: center;
            padding: 30px 15px;
        }

        /* A4 landscape = 297mm x 210mm */
        .certificate {
            width: 297mm;
            height: 210mm;
            background: #fffdf8;
            position: relative;
            box-shadow: 0 0 25px rgba(0, 0, 0, .15);
            overflow: hidden;
        }

        .certificate::before {
            content: "";
            position: absolute;
            inset: 10mm;
            border: 3px solid #b8860b;
        }

        .certificate::after {
            content: "";
            position: absolute;
            inset: 13mm;
            border: 1px solid #b8860b;
        }

        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid #b8860b;
        }

        .corner.tl {
            top: 8mm;
            left: 8mm;
            border-right: none;
            border-bottom: none;
        }

        .corner.tr {
            top: 8mm;
            right: 8mm;
            border-left: none;
            border-bottom: none;
        }

        .corner.bl {
            bottom: 8mm;
            left: 8mm;
            border-right: none;
            border-top: none;
        }

        .corner.br {
            bottom: 8mm;
            right: 8mm;
            border-left: none;
            border-top: none;
        }

        .cert-content {
            position: relative;
            z-index: 2;
            height: 100%;
            padding: 22mm 28mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .org-name {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #2c2c2c;
            text-transform: uppercase;
        }

        .org-sub {
            font-size: 12px;
            letter-spacing: 4px;
            color: #888;
            text-transform: uppercase;
            margin-top: 4px;
            font-family: Arial, sans-serif;
        }

        .cert-title {
            margin-top: 18px;
            font-size: 40px;
            font-weight: 700;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cert-subtitle {
            font-size: 14px;
            color: #555;
            font-family: Arial, sans-serif;
            margin-top: 6px;
            letter-spacing: 1px;
        }

        .divider {
            width: 90px;
            height: 3px;
            background: #b8860b;
            margin: 18px 0;
        }

        .presented-text {
            font-size: 15px;
            color: #444;
            font-family: Arial, sans-serif;
        }

        .student-name {
            font-family: 'Great Vibes', cursive;
            font-size: 56px;
            color: #1a1a1a;
            margin: 10px 0 4px;
            line-height: 1;
        }

        .student-meta {
            font-size: 12px;
            color: #777;
            font-family: Arial, sans-serif;
            margin-bottom: 14px;
        }

        .achievement-text {
            font-size: 15px;
            color: #444;
            font-family: Arial, sans-serif;
            max-width: 620px;
            line-height: 1.6;
        }

        .promotion-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0;
            padding: 10px 26px;
            border: 2px solid #b8860b;
            border-radius: 40px;
            background: #fff8e8;
        }

        .promotion-badge img {
            height: 34px;
            width: 34px;
            object-fit: contain;
        }

        .promotion-badge .promo-name {
            font-size: 20px;
            font-weight: 700;
            color: #8a6200;
        }

        .stats-row {
            display: flex;
            gap: 40px;
            margin-top: 6px;
            font-family: Arial, sans-serif;
        }

        .stat-box {
            text-align: center;
        }

        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #2c2c2c;
            margin-top: 2px;
        }

        .footer-row {
            margin-top: auto;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 20px;
            font-family: Arial, sans-serif;
        }

        .sign-block {
            width: 200px;
            text-align: center;
        }

        .sign-line {
            border-top: 1px solid #333;
            margin-bottom: 6px;
        }

        .sign-label {
            font-size: 11px;
            color: #555;
        }

        .seal {
            width: 90px;
            height: 90px;
            border: 2px dashed #b8860b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #b8860b;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: Arial, sans-serif;
            font-weight: 700;
        }

        .cert-no {
            position: absolute;
            bottom: 14mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            color: #999;
            font-family: Arial, sans-serif;
            letter-spacing: 1px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }

            html,
            body {
                background: #fff !important;
            }

            .toolbar {
                display: none !important;
            }

            .page-wrap {
                padding: 0 !important;
            }

            .certificate {
                box-shadow: none !important;
                width: 297mm;
                height: 210mm;
                page-break-after: avoid;
            }
        }
    </style>

    {{-- Screen-only toolbar --}}
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print Certificate
        </button>
        <a href="javascript:window.close()" class="btn-close">
            ✕ Close
        </a>
    </div>

    <div class="page-wrap">
        <div class="certificate">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>

            <div class="cert-content">

                <div class="org-name">
                    {{ $student->organisation?->name ?? 'MindShiksha' }}
                </div>
                <div class="org-sub">Assessment &amp; Development Program</div>

                <div class="cert-title">Certificate of Achievement</div>
                <div class="cert-subtitle">{{ $typeLabel }} PROMOTION CERTIFICATE</div>

                <div class="divider"></div>

                <div class="presented-text">This certificate is proudly presented to</div>

                <div class="student-name">{{ $student->full_name }}</div>
                <div class="student-meta">
                    Student ID: {{ $student->details?->student_id ?? 'N/A' }}
                </div>

                <div class="achievement-text">
                    for successfully demonstrating exceptional performance and has been
                    promoted to the level of
                </div>

                <div class="promotion-badge">
                    @if ($promotion?->badge)
                        <img src="{{ asset('storage/' . $promotion->badge) }}" alt="badge">
                    @endif
                    <span class="promo-name">{{ $promotion?->name ?? 'N/A' }}</span>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-label">Age Group</div>
                        <div class="stat-value">{{ $ageGroup }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Score</div>
                        <div class="stat-value">
                            {{ $attempt->total_score ?? 0 }} / {{ $assessment->total_marks ?? '?' }}
                            ({{ $percentage }}%)
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Date Achieved</div>
                        <div class="stat-value">{{ $issuedDate }}</div>
                    </div>
                </div>

                <div class="footer-row">
                    <div class="sign-block">
                        <div class="sign-line"></div>
                        <div class="sign-label">Examiner / Evaluator</div>
                    </div>

                    <div class="seal">Official<br>Seal</div>

                    <div class="sign-block">
                        <div class="sign-line"></div>
                        <div class="sign-label">Director / Principal</div>
                    </div>
                </div>

            </div>

            <div class="cert-no">Certificate No: {{ $certNo }}</div>
        </div>
    </div>

</div>
