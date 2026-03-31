@extends('admin.layouts.master')

@section('title', 'Courses Report')
@section('page-icon', 'fa-book')
@section('page-title', 'Courses Report')

@push('styles')
<style>
.report-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.report-header h1 {
    font-weight: 600;
    letter-spacing: 0.3px;
}

.filter-section {
    background: #ffffff;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 28px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.filter-section h5 {
    font-weight: 600;
    color: #1e293b;
}

.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}

.stat-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 18px rgba(0,0,0,0.06);
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
}

.stat-label {
    color: #64748b;
    font-size: 0.8rem;
    margin-top: 4px;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
}

.course-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px;
    border: 1px solid #e5e7eb;
    transition: all 0.25s ease;
}

.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(0,0,0,0.08);
    border-color: #6366f1;
}

.course-name {
    font-size: 1.15rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 6px;
}

.course-code {
    display: inline-block;
    background: #eef2ff;
    color: #4f46e5;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 999px;
    margin-bottom: 10px;
}

.course-stats {
    display: flex;
    justify-content: space-between;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #e5e7eb;
    font-size: 0.8rem;
    color: #475569;
}

.btn-export {
    background: #059669;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    transition: 0.2s ease;
}

.btn-export:hover {
    background: #047857;
}

.btn-filter {
    background: #4f46e5;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    border: none;
    font-size: 0.85rem;
    transition: 0.2s ease;
}

.btn-filter:hover {
    background: #4338ca;
}

.btn-clear {
    background: #f1f5f9;
    color: #334155;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    transition: 0.2s ease;
    text-decoration: none;
}

.btn-clear:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.form-control {
    border-radius: 8px;
    font-size: 0.85rem;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99,102,241,0.15);
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    background: #f9fafb;
    border-radius: 14px;
    border: 1px dashed #e5e7eb;
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="report-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2" style="font-size: 1.6rem;">
                    <i class="fas fa-book me-2"></i>Courses Report
                </h1>
                <p class="mb-0 text-white-50 small">
                    Comprehensive overview of all courses and their units
                </p>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Report</h5>

        <form method="GET" action="{{ route('admin.reports.courses') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Search Course</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Course name or code..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="from" class="form-control"
                       value="{{ request('from', $dateFrom) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="to" class="form-control"
                       value="{{ request('to', $dateTo) }}">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.reports.courses') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-label">Total Courses</div>
            <div class="stat-value">{{ $data->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Units</div>
            <div class="stat-value">{{ $data->sum('units_count') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Avg Units per Course</div>
            <div class="stat-value">
                {{ $data->avg('units_count') ? round($data->avg('units_count'), 1) : 0 }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Date Range</div>
            <div class="stat-value">
                {{ request('from') ?: 'All' }} - {{ request('to') ?: 'All' }}
            </div>
        </div>
    </div>

    <!-- Export Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.reports.export-csv', [
            'type' => 'courses',
            'from' => request('from', $dateFrom),
            'to' => request('to', $dateTo),
            'search' => request('search')
        ]) }}" class="btn-export">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid">
        @forelse($data as $course)
            <div class="course-card">

                <div class="course-name">{{ $course->name }}</div>

                <div class="course-code">{{ $course->code }}</div>

                <div class="course-stats">
                    <span class="d-flex align-items-center gap-1">
                        <i class="fas fa-layer-group text-primary"></i>
                        {{ $course->units_count }} Units
                    </span>

                    <span class="d-flex align-items-center gap-1">
                        <i class="fas fa-calendar text-muted"></i>
                        {{ $course->created_at->format('M d, Y') }}
                    </span>
                </div>

            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                <p class="text-muted">No courses found for the selected filters.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection