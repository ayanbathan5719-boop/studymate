@extends('student.layouts.master')

@section('title', 'My Reports')
@section('page-icon', 'fa-chart-bar')
@section('page-title', 'My Learning Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css">
<style>
    .reports-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .reports-header h1 {
        font-size: 2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .reports-header h1 i {
        color: #f59e0b;
    }

    .unit-selector {
        min-width: 300px;
    }

    .unit-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        background: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
    }

    .stat-header i {
        font-size: 1.5rem;
        color: #f59e0b;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .stat-sub {
        color: #94a3b8;
        font-size: 0.85rem;
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

    .chart-card h2 {
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-card h2 i {
        color: #f59e0b;
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    .unit-breakdown {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        margin-top: 30px;
    }

    .unit-breakdown h2 {
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .unit-breakdown h2 i {
        color: #f59e0b;
    }

    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
    }

    .breakdown-table th {
        text-align: left;
        padding: 12px;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .breakdown-table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }

    .breakdown-table tr:hover {
        background: #f8fafc;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 4px;
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
        margin-bottom: 25px;
    }

    @media (max-width: 768px) {
        .charts-row {
            grid-template-columns: 1fr;
        }
        
        .breakdown-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="reports-container">
    <div class="reports-header">
        <h1><i class="fas fa-chart-bar"></i> My Learning Reports</h1>
        
        <div class="unit-selector">
            <form method="GET" action="{{ route('student.reports.index') }}">
                <select name="unit_id" class="unit-select" onchange="this.form.submit()">
                    <option value="">All Units (Overall)</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ $selectedUnit && $selectedUnit->id == $unit->id ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($reportData)
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3>Study Time</h3>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $selectedUnit ? $reportData['study']['total_formatted'] : $reportData['study']['total_formatted'] }}</div>
                <div class="stat-sub">
                    @if($selectedUnit)
                        {{ $reportData['study']['total_minutes'] }} minutes
                    @else
                        {{ $reportData['study']['total_hours'] }} hours total
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Resources</h3>
                    <i class="fas fa-file"></i>
                </div>
                <div class="stat-value">{{ $selectedUnit ? $reportData['resources']['total'] : $reportData['resources']['total'] }}</div>
                <div class="stat-sub">available in this unit</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Forum Activity</h3>
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-value">{{ $selectedUnit ? $reportData['forum']['total'] : $reportData['forum']['total'] }}</div>
                <div class="stat-sub">{{ $reportData['forum']['posts'] }} posts · {{ $reportData['forum']['replies'] }} replies</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <h3>Deadlines</h3>
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value">{{ $reportData['deadlines']['completion_rate'] }}%</div>
                <div class="stat-sub">{{ $reportData['deadlines']['completed'] }}/{{ $reportData['deadlines']['accepted'] }} completed</div>
            </div>
        </div>

        @if($selectedUnit && isset($reportData['study']['weekly']))
            <!-- Weekly Activity Chart -->
            <div class="chart-card">
                <h2><i class="fas fa-chart-line"></i> Weekly Study Pattern</h2>
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Top Resources -->
            @if($reportData['resources']['top']->count() > 0)
            <div class="unit-breakdown">
                <h2><i class="fas fa-star"></i> Most Accessed Resources</h2>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Downloads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['resources']['top'] as $resource)
                            <tr>
                                <td>{{ $resource->title }}</td>
                                <td>{{ $resource->download_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @else
            <!-- Unit Breakdown -->
            <div class="unit-breakdown">
                <h2><i class="fas fa-layer-group"></i> Unit Breakdown</h2>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Study Time</th>
                            <th>Resources</th>
                            <th>Forum Posts</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['unit_breakdown'] as $unit)
                            <tr>
                                <td><strong>{{ $unit['code'] }}</strong><br><small>{{ $unit['name'] }}</small></td>
                                <td>{{ $unit['study_time'] }}</td>
                                <td>{{ $unit['resources'] }}</td>
                                <td>{{ $unit['forum_posts'] }}</td>
                                <td style="width: 200px;">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ min(($unit['study_minutes'] / 120) * 100, 100) }}%"></div>
                                    </div>
                                    <small style="color: #64748b;">{{ $unit['study_minutes'] }} / 120 min goal</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>No Data Available</h3>
            <p>Start studying to see your learning analytics!</p>
            <a href="{{ route('student.units.available') }}" class="btn-primary">Enroll in Units</a>
        </div>
    @endif
</div>

@if($reportData && $selectedUnit && isset($reportData['study']['weekly']))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const weeklyData = @json($reportData['study']['weekly']);
        
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: weeklyData.map(d => d.date),
                datasets: [{
                    label: 'Study Time (minutes)',
                    data: weeklyData.map(d => d.minutes),
                    backgroundColor: '#f59e0b',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Minutes'
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection