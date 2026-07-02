<div class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding-top: 2rem; padding-bottom: 4rem;">
    <div class="container-fluid px-4">

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- HEADER SECTION --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <h1 class="mb-2 fw-bold text-white" style="font-size: 2.5rem; letter-spacing: -0.5px;">
                            Dashboard Overview
                        </h1>
                        <p class="text-white mb-0 opacity-75" style="font-size: 1.1rem;">
                            Welcome back, <strong>{{ auth()->user()->name }}</strong>
                        </p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="text-end text-white opacity-75 me-3 d-none d-md-block">
                            <p class="mb-0 small">Last updated</p>
                            <p class="mb-0 fw-semibold">{{ now()->format('d M Y, H:i') }}</p>
                        </div>
                        <button wire:click="$refresh" class="btn btn-light btn-lg shadow-sm">
                            <i class="ri ri-refresh-line me-2"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- PRIMARY METRICS - GLASSMORPHISM CARDS --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5">
            {{-- Total Assessments --}}
            <div class=" col-sm-6 col-xl-3">
                <div class="card h-100 position-relative overflow-hidden" style="background: rgba(255,255,255,0.95);">

                    <div class="card-body p-4 position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <p class="text-white-50 text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">
                                    Total Assessments
                                </p>
                                <h2 class="text-dark mb-0 fw-bold" style="font-size: 2.5rem;">
                                    {{ number_format($this->totalAssessments) }}
                                </h2>
                            </div>
                            <div class="metric-icon bg-gradient-primary">
                                <i class="ri ri-file-list-3-line"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white-50">
                            <i class="ri ri-arrow-up-line text-success"></i>
                            <span class="small">{{ $this->publishedAssessments }} Published</span>
                        </div>
                        <div class="progress mt-3" style="height: 4px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar bg-gradient-primary" role="progressbar"
                                 style="width: {{ $this->totalAssessments > 0 ? ($this->publishedAssessments / $this->totalAssessments * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Questions --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 position-relative overflow-hidden" style="background: rgba(255,255,255,0.95);">

                <div class="card-body p-4 position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <p class="text-white-50 text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">
                                    Total Questions
                                </p>
                                <h2 class="text-dark mb-0 fw-bold" style="font-size: 2.5rem;">
                                    {{ number_format($this->totalQuestions) }}
                                </h2>
                            </div>
                            <div class="metric-icon bg-gradient-info">
                                <i class="ri ri-question-line"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white-50">
                            <i class="ri ri-stack-line"></i>
                            <span class="small">{{ $this->totalQuestionGroups }} Question Groups</span>
                        </div>
                        <div class="progress mt-3" style="height: 4px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Students --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 position-relative overflow-hidden" style="background: rgba(255,255,255,0.95);">

                <div class="card-body p-4 position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <p class="text-white-50 text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">
                                    Total Students
                                </p>
                                <h2 class="text-dark mb-0 fw-bold" style="font-size: 2.5rem;">
                                    {{ number_format($this->totalStudents) }}
                                </h2>
                            </div>
                            <div class="metric-icon bg-gradient-success">
                                <i class="ri ri-user-line"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white-50">
                            <i class="ri ri-checkbox-circle-line text-success"></i>
                            <span class="small">{{ $this->activeStudents }} Active Users</span>
                        </div>
                        <div class="progress mt-3" style="height: 4px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar bg-gradient-success" role="progressbar"
                                 style="width: {{ $this->totalStudents > 0 ? ($this->activeStudents / $this->totalStudents * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Attempts --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 position-relative overflow-hidden" style="background: rgba(255,255,255,0.95);">

                <div class="card-body p-4 position-relative" style="z-index: 1;">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <p class="text-white-50 text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">
                                    Total Attempts
                                </p>
                                <h2 class="text-dark mb-0 fw-bold" style="font-size: 2.5rem;">
                                    {{ number_format($this->totalAttempts) }}
                                </h2>
                            </div>
                            <div class="metric-icon bg-gradient-warning">
                                <i class="ri ri-file-edit-line"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white-50">
                            <i class="ri ri-time-line text-warning"></i>
                            <span class="small">{{ $this->inProgressAttempts }} In Progress</span>
                        </div>
                        <div class="progress mt-3" style="height: 4px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- SECONDARY METRICS - COMPACT STATS --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-5">
            <div class="col-lg-3 col-sm-6">
                <div class="glass-card-sm">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white mb-1 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Organisations</p>
                            <h4 class="text-white mb-0 fw-bold">{{ number_format($this->totalOrganisations) }}</h4>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-25">
                            <i class="ri ri-building-line text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="glass-card-sm">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white mb-1 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Evaluated</p>
                            <h4 class="text-white mb-0 fw-bold">{{ number_format($this->evaluatedAttempts) }}</h4>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-25">
                            <i class="ri ri-check-double-line text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="glass-card-sm">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white mb-1 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Avg Score</p>
                            <h4 class="text-white mb-0 fw-bold">{{ $this->averageScore }}</h4>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-25">
                            <i class="ri ri-trophy-line text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="glass-card-sm">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white mb-1 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Accuracy</p>
                            <h4 class="text-white mb-0 fw-bold">{{ $this->accuracyRate }}%</h4>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-25">
                            <i class="ri ri-percent-line text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- CHARTS SECTION --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5">

            {{-- Assessment by Type --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-pie-chart-2-line me-2 text-primary"></i>
                                Assessment Types
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                {{ $this->assessmentsByType->count() }} Types
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($this->assessmentsByType->count() > 0)
                            <div style="height: 260px;" class="mb-3">
                                <canvas id="typeChart"></canvas>
                            </div>
                            <div class="chart-legend">
                                @foreach($this->assessmentsByType as $type)
                                    <div class="legend-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="legend-color" style="background-color: {{ $this->getTypeColor($type['type']) }};"></div>
                                            <span class="legend-label">{{ strtoupper($type['type']) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="legend-value">{{ $type['count'] }}</span>
                                            <span class="legend-percent">{{ $type['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="ri ri-pie-chart-line"></i>
                                <p>No data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attempt Status --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-donut-chart-line me-2 text-success"></i>
                                Attempt Status
                            </h5>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                Live
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($this->attemptsByStatus->count() > 0)
                            <div style="height: 260px;" class="mb-3">
                                <canvas id="attemptStatusChart"></canvas>
                            </div>
                            <div class="chart-legend">
                                @foreach($this->attemptsByStatus as $status)
                                    <div class="legend-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="legend-color" style="background-color: {{ $this->getStatusColor($status['status']) }};"></div>
                                            <span class="legend-label">{{ ucfirst(str_replace('_', ' ', $status['status'])) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="legend-value">{{ $status['count'] }}</span>
                                            <span class="legend-percent">{{ $status['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="ri ri-donut-chart-line"></i>
                                <p>No data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Questions by Difficulty --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-bar-chart-line me-2 text-warning"></i>
                                Difficulty Levels
                            </h5>
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                Questions
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($this->questionsByDifficulty->count() > 0)
                            <div style="height: 260px;" class="mb-3">
                                <canvas id="difficultyChart"></canvas>
                            </div>
                            <div class="chart-legend">
                                @foreach($this->questionsByDifficulty as $diff)
                                    <div class="legend-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="legend-color" style="background-color: {{ $this->getDifficultyColor($diff['difficulty']) }};"></div>
                                            <span class="legend-label">{{ $diff['difficulty'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="legend-value">{{ $diff['count'] }}</span>
                                            <span class="legend-percent">{{ $diff['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="ri ri-bar-chart-line"></i>
                                <p>No data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TREND CHARTS --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5">
            {{-- Assessment Trend --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                            <i class="ri ri-line-chart-line me-2 text-primary"></i>
                            Assessment Creation Trend
                        </h5>
                        <p class="text-muted small mb-3 mt-1">Last 30 days activity</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($this->assessmentTrend->count() > 0)
                            <div style="height: 300px;">
                                <canvas id="assessmentTrendChart"></canvas>
                            </div>
                        @else
                            <div class="empty-state" style="height: 300px;">
                                <i class="ri ri-line-chart-line"></i>
                                <p>No trend data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attempt Trend --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                            <i class="ri ri-line-chart-line me-2 text-success"></i>
                            Test Attempts Trend
                        </h5>
                        <p class="text-muted small mb-3 mt-1">Student participation over time</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($this->attemptTrend->count() > 0)
                            <div style="height: 300px;">
                                <canvas id="attemptTrendChart"></canvas>
                            </div>
                        @else
                            <div class="empty-state" style="height: 300px;">
                                <i class="ri ri-line-chart-line"></i>
                                <p>No trend data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TABLES --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5">

            {{-- Top Organisations --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-building-line me-2 text-primary"></i>
                                Top Organisations
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover modern-table mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-4">Organisation</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-center">Limit</th>
                                    <th class="text-end pe-4">Usage</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($this->topOrganisations as $org)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-circle">
                                                    {{ strtoupper(substr($org['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold text-dark">{{ Str::limit($org['name'], 25) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                                    {{ $org['students'] }}
                                                </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ $org['max_students'] == 0 ? '∞' : $org['max_students'] }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            @if($org['max_students'] > 0)
                                                @php
                                                    $percentage = round(($org['students'] / $org['max_students']) * 100, 1);
                                                    $color = $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                                                @endphp
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <span class="small fw-semibold text-{{ $color }}">{{ $percentage }}%</span>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="ri ri-building-line d-block mb-2" style="font-size: 2rem;"></i>
                                            No organisations found
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Most Attempted Assessments --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-fire-line me-2 text-danger"></i>
                                Most Popular
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-danger rounded-pill px-3">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover modern-table mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Title</th>
                                    <th class="text-end pe-4">Attempts</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($this->mostAttemptedAssessments as $assessment)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-light text-dark border fw-semibold">{{ $assessment['code'] }}</span>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-500 text-truncate" style="max-width: 250px;">{{ $assessment['title'] }}</p>
                                        </td>
                                        <td class="text-end pe-4">
                                                <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2 fw-bold">
                                                    <i class="ri ri-fire-fill me-1"></i>{{ $assessment['attempts'] }}
                                                </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="ri ri-fire-line d-block mb-2" style="font-size: 2rem;"></i>
                                            No attempts found
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- RECENT ATTEMPTS TABLE --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold" style="color: #1a202c;">
                                <i class="ri ri-time-line me-2 text-primary"></i>
                                Recent Test Attempts
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="ri ri-filter-3-line me-1"></i> Filter
                                </button>
                                <a href="#" class="btn btn-sm btn-primary rounded-pill px-3">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover modern-table mb-0">
                            <thead>
                            <tr>
                                <th class="ps-4">ACK No</th>
                                <th>Student</th>
                                <th>Assessment</th>
                                <th>Started At</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Score</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($this->recentAttempts as $attempt)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border fw-semibold">{{ $attempt->ack_no }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle-sm bg-success">
                                                {{ strtoupper(substr($attempt->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="fw-500">{{ $attempt->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-truncate" style="max-width: 200px;">{{ $attempt->assessment->title ?? 'N/A' }}</p>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('d M Y, H:i') : '—' }}
                                        </small>
                                    </td>
                                    <td>
                                            <span class="status-badge status-{{ $attempt->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                            </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($attempt->status === 'evaluated')
                                            <span class="score-badge">{{ $attempt->total_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="ri ri-inbox-line"></i>
                                            <p>No recent attempts</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- STYLES --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<style>
    /* Glass Card Effects */
    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .glass-card-sm {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 15px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .glass-card-sm:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.18);
    }

    /* Metric Icons */
    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* Icon Circle */
    .icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Chart Legend */
    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .legend-item:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }

    .legend-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
    }

    .legend-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212529;
    }

    .legend-percent {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        color: #adb5bd;
    }

    .empty-state i {
        font-size: 64px;
        opacity: 0.5;
        margin-bottom: 16px;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Modern Table */
    .modern-table thead {
        background: transparent;
        border-bottom: 2px solid #e9ecef;
    }

    .modern-table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 16px 12px;
        border: none;
    }

    .modern-table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
        border-top: 1px solid #f1f3f5;
        font-size: 0.9rem;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }

    /* Avatar Circle */
    .avatar-circle {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .avatar-circle-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-evaluated {
        background: #d4edda;
        color: #155724;
    }

    .status-submitted {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-in_progress {
        background: #fff3cd;
        color: #856404;
    }

    /* Score Badge */
    .score-badge {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .glass-card, .glass-card-sm {
            border-radius: 15px;
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            font-size: 24px;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
        }
    }
</style>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- CHART.JS SCRIPTS --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endassets

@script
<script>
    function initCharts() {
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            }
        };

        // Destroy existing charts if they exist
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.destroy();
        });

        // Type Chart
        const typeCtx = document.getElementById('typeChart');
        if (typeCtx) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($this->assessmentsByType->pluck('type')->map(fn($t) => strtoupper($t))->toArray()),
                    datasets: [{
                        data: @json($this->assessmentsByType->pluck('count')->toArray()),
                        backgroundColor: @json($this->assessmentsByType->pluck('type')->map(fn($t) => $this->getTypeColor($t))->toArray()),
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: chartConfig
            });
        }

        // Attempt Status Chart
        const attemptStatusCtx = document.getElementById('attemptStatusChart');
        if (attemptStatusCtx) {
            new Chart(attemptStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($this->attemptsByStatus->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))->toArray()),
                    datasets: [{
                        data: @json($this->attemptsByStatus->pluck('count')->toArray()),
                        backgroundColor: @json($this->attemptsByStatus->pluck('status')->map(fn($s) => $this->getStatusColor($s))->toArray()),
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: chartConfig
            });
        }

        // Difficulty Chart
        const difficultyCtx = document.getElementById('difficultyChart');
        if (difficultyCtx) {
            new Chart(difficultyCtx, {
                type: 'bar',
                data: {
                    labels: @json($this->questionsByDifficulty->pluck('difficulty')->toArray()),
                    datasets: [{
                        label: 'Questions',
                        data: @json($this->questionsByDifficulty->pluck('count')->toArray()),
                        backgroundColor: @json($this->questionsByDifficulty->pluck('difficulty')->map(fn($d) => $this->getDifficultyColor($d))->toArray()),
                        borderRadius: 10,
                        borderSkipped: false,
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Assessment Trend
        const assessmentTrendCtx = document.getElementById('assessmentTrendChart');
        if (assessmentTrendCtx) {
            new Chart(assessmentTrendCtx, {
                type: 'line',
                data: {
                    labels: @json($this->assessmentTrend->pluck('date')->toArray()),
                    datasets: [{
                        label: 'Assessments',
                        data: @json($this->assessmentTrend->pluck('count')->toArray()),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Attempt Trend
        const attemptTrendCtx = document.getElementById('attemptTrendChart');
        if (attemptTrendCtx) {
            new Chart(attemptTrendCtx, {
                type: 'line',
                data: {
                    labels: @json($this->attemptTrend->pluck('date')->toArray()),
                    datasets: [{
                        label: 'Attempts',
                        data: @json($this->attemptTrend->pluck('count')->toArray()),
                        borderColor: '#43e97b',
                        backgroundColor: 'rgba(67, 233, 123, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#43e97b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    // Initialize charts when component loads
    $wire.on('chartDataUpdated', () => {
        setTimeout(() => initCharts(), 100);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initCharts);

    // Re-initialize after Livewire navigation
    document.addEventListener('livewire:navigated', initCharts);
</script>
@endscript
