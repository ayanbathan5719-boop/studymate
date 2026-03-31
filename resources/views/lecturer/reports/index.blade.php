@extends('lecturer.layouts.master')
@section('title', 'Reports')
@section('page-title', 'Unit Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css">
<style>
    .reports-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .header-section {
        margin-bottom: 30px;
    }

    .header-section h1 {
        font-size: 2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-section h1 i {
        color: #f59e0b;
    }

    .filters-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .filter-group label i {
        color: #f59e0b;
        margin-right: 5px;
    }

    .filter-select, .filter-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .filter-select:focus, .filter-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    .btn-export {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-export-csv {
        background: #10b981;
        color: white;
    }

    .btn-export-csv:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-export-pdf {
        background: #dc2626;
        color: white;
    }

    .btn-export-pdf:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-header h3 {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
    }

    .stat-header i {
        font-size: 1.5rem;
        color: #f59e0b;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
    }

    .charts-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
    }

    .chart-card h3 {
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-card h3 i {
        color: #f59e0b;
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .details-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
    }

    .details-card h3 {
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .details-card h3 i {
        color: #f59e0b;
    }

    .resource-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .resource-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .resource-item:last-child {
        border-bottom: none;
    }

    .resource-title {
        font-weight: 500;
        color: #1e293b;
    }

    .resource-downloads {
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #475569;
    }

    .type-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #334155;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
    }

    @media (max-width: 768px) {
        .filters-form {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .filter-actions {
            width: 100%;
        }
        
        .btn-primary, .btn-secondary {
            width: 100%;
            justify-content: center;
        }
        
        .charts-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="reports-container">
    <div class="header-section">
        <h1><i class="fas fa-chart-bar"></i> Unit Reports</h1>
    </div>

    <!-- Unit Selection & Filters -->
    <div class="filters-card">
        <form method="GET" action="{{ route('lecturer.reports.index') }}" class="filters-form">
            <div class="filter-group">
                <label><i class="fas fa-layer-group"></i> Select Unit</label>
                <select name="unit_id" class="filter-select" required>
                    <option value="">Choose a unit...</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ ($filters['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar-alt"></i> From Date</label>
                <input type="date" name="from" class="filter-input" value="{{ $filters['from'] ?? '' }}">
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar-alt"></i> To Date</label>
                <input type="date" name="to" class="filter-input" value="{{ $filters['to'] ?? '' }}">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter"></i> Generate Report
                </button>
                <a href="{{ route('lecturer.reports.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>

            @if($selectedUnit)
                <div class="export-buttons">
                    <a href="{{ route('lecturer.reports.export-csv', $filters) }}" class="btn-export btn-export-csv">
                        <i class="fas fa-file-csv"></i> CSV
                    </a>
                    <a href="{{ route('lecturer.reports.export-pdf', $filters) }}" class="btn-export btn-export-pdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if($selectedUnit && $reportData)
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3>Total Students</h3>
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $reportData['students']['total'] }}</div>
                <div class="stat-label">
                    {{ $reportData['students']['active'] }} active ({{ $reportData['students']['engagement_rate'] }}%)
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Resources</h3>
                    <i class="fas fa-file"></i>
                </div>
                <div class="stat-value">{{ $reportData['resources']['total'] }}</div>
                <div class="stat-label">{{ $reportData['resources']['total_downloads'] }} total downloads</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Forum Activity</h3>
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-value">{{ $reportData['forum']['total_posts'] }}</div>
                <div class="stat-label">{{ $reportData['forum']['total_replies'] }} replies</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Study Time</h3>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $reportData['study_time']['total_hours'] }}h</div>
                <div class="stat-label">{{ $reportData['study_time']['average_per_student'] }} min/student</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-row">
            <div class="chart-card">
                <h3><i class="fas fa-chart-line"></i> Weekly Activity</h3>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3><i class="fas fa-chart-pie"></i> Resources by Type</h3>
                <div class="chart-container">
                    <canvas id="resourcesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="details-grid">
            <div class="details-card">
                <h3><i class="fas fa-file-alt"></i> Top Resources</h3>
                <ul class="resource-list">
                    @foreach($reportData['resources']['top_resources'] as $resource)
                        <li class="resource-item">
                            <span class="resource-title">{{ Str::limit($resource->title, 30) }}</span>
                            <span class="resource-downloads">
                                <i class="fas fa-download"></i> {{ $resource->download_count }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="details-card">
                <h3><i class="fas fa-chart-pie"></i> Resources by Type</h3>
                <ul class="resource-list">
                    @foreach($reportData['resources']['by_type'] as $type => $data)
                        <li class="resource-item">
                            <span class="resource-title">
                                <span class="type-badge">{{ strtoupper($type) }}</span>
                            </span>
                            <span>
                                {{ $data['count'] }} files • {{ $data['downloads'] }} downloads
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="details-card">
                <h3><i class="fas fa-comments"></i> Forum Summary</h3>
                <ul class="resource-list">
                    <li class="resource-item">
                        <span>Posts by Lecturer</span>
                        <span class="resource-downloads">{{ $reportData['forum']['posts_by_lecturer'] }}</span>
                    </li>
                    <li class="resource-item">
                        <span>Posts by Students</span>
                        <span class="resource-downloads">{{ $reportData['forum']['posts_by_students'] }}</span>
                    </li>
                    <li class="resource-item">
                        <span>Total Replies</span>
                        <span class="resource-downloads">{{ $reportData['forum']['total_replies'] }}</span>
                    </li>
                    <li class="resource-item">
                        <span>Active Students</span>
                        <span class="resource-downloads">{{ $reportData['forum']['active_students'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    @elseif($units->isNotEmpty())
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>Select a Unit</h3>
            <p>Choose a unit from the dropdown above to view its reports.</p>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-exclamation-circle"></i>
            <h3>No Units Assigned</h3>
            <p>You haven't been assigned any units yet.</p>
        </div>
    @endif
</div>

@if($selectedUnit && $reportData)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Activity Chart
    const ctx1 = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($reportData['weekly_activity']['dates']) !!},
            datasets: [
                {
                    label: 'Posts',
                    data: {!! json_encode($reportData['weekly_activity']['posts']) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Replies',
                    data: {!! json_encode($reportData['weekly_activity']['replies']) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Study Time (min)',
                    data: {!! json_encode($reportData['weekly_activity']['study_time']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Minutes'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Resources Chart
    const ctx2 = document.getElementById('resourcesChart').getContext('2d');
    const resourceTypes = {!! json_encode(array_keys($reportData['resources']['by_type']->toArray())) !!};
    const resourceCounts = {!! json_encode(array_column($reportData['resources']['by_type']->toArray(), 'count')) !!};
    
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: resourceTypes.map(type => type.toUpperCase()),
            datasets: [{
                data: resourceCounts,
                backgroundColor: [
                    '#f59e0b',
                    '#667eea',
                    '#10b981',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endif
@endsection