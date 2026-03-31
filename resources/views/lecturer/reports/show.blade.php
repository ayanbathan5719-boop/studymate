@extends('lecturer.layouts.master')

@section('title', 'Report Details')
@section('page-icon', 'fa-chart-bar')
@section('page-title', 'Report Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/lecturer/reports">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Report Details</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="report-show-container">
    <div class="row">
        <div class="col-md-12">
            <div class="report-header">
                <h2>Unit Report: {{ $unit->code }} - {{ $unit->name }}</h2>
                <div class="header-actions">
                    <a href="{{ route('lecturer.reports.export-pdf', ['unit' => $unit->id]) }}" class="btn-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('lecturer.reports.export-csv', ['unit' => $unit->id]) }}" class="btn-csv">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Report Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Generated:</span>
                        <span class="info-value">{{ now()->format('F d, Y H:i:s') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date Range:</span>
                        <span class="info-value">{{ $dateFrom ?? 'All' }} to {{ $dateTo ?? 'All' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Course:</span>
                        <span class="info-value">{{ $unit->course->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Students:</span>
                        <span class="info-value">{{ $data['students']['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3>Students</h3>
                    <p class="stat-value">{{ $data['students']['total'] }}</p>
                    <p class="stat-label">{{ $data['students']['active'] }} active ({{ $data['students']['engagement_rate'] }}%)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file"></i></div>
                <div class="stat-content">
                    <h3>Resources</h3>
                    <p class="stat-value">{{ $data['resources']['total'] }}</p>
                    <p class="stat-label">{{ $data['resources']['total_downloads'] }} downloads</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-comments"></i></div>
                <div class="stat-content">
                    <h3>Forum Posts</h3>
                    <p class="stat-value">{{ $data['forum']['total_posts'] }}</p>
                    <p class="stat-label">{{ $data['forum']['total_replies'] }} replies</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <h3>Study Time</h3>
                    <p class="stat-value">{{ $data['study_time']['total_hours'] }}h</p>
                    <p class="stat-label">{{ $data['study_time']['average_per_student'] }} min/student</p>
                </div>
            </div>
        </div>
    </div>

    <style>
    .report-show-container {
        padding: 20px 0;
    }

    .row {
        margin-bottom: 25px;
    }

    .report-header {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .report-header h2 {
        margin: 0;
        color: #1e293b;
        font-size: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-pdf, .btn-csv {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-pdf {
        background: #ef4444;
        color: white;
    }

    .btn-pdf:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .btn-csv {
        background: #10b981;
        color: white;
    }

    .btn-csv:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }

    .info-card h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #1e293b;
        font-size: 1.1rem;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
    }

    .info-card h3 i {
        color: #f59e0b;
        margin-right: 8px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
    }

    .info-label {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        margin-bottom: 5px;
    }

    .info-value {
        display: block;
        color: #1e293b;
        font-weight: 600;
        font-size: 1rem;
    }

    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0 10px;
        box-sizing: border-box;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 24px;
    }

    .stat-content {
        flex: 1;
    }

    .stat-content h3 {
        margin: 0 0 5px 0;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .stat-value {
        margin: 0 0 3px 0;
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-label {
        margin: 0;
        color: #94a3b8;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
            margin-bottom: 15px;
        }
        
        .report-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 480px) {
        .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    </style>
</div>
@endsection