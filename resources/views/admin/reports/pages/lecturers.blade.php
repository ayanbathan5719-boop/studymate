@extends('admin.layouts.master')

@section('title', 'Lecturers Report')
@section('page-icon', 'fa-chalkboard-user')
@section('page-title', 'Lecturers Report')

@push('styles')
<style>
.report-header {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.report-header h1 {
    font-weight: 600;
}

.filter-section {
    background: #ffffff;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 28px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
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
    transition: 0.2s ease;
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

.lecturers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
}

.lecturer-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    border: 1px solid #e5e7eb;
    transition: all 0.25s ease;
}

.lecturer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(0,0,0,0.08);
    border-color: #d97706;
}

.lecturer-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.6rem;
    color: white;
    font-weight: 600;
}

.lecturer-name {
    font-size: 1.05rem;
    font-weight: 600;
    color: #0f172a;
}

.lecturer-email {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 6px;
}

.badge-soft {
    background: #fef3c7;
    color: #92400e;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
}

.badge-muted {
    background: #f1f5f9;
    color: #475569;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
}

.btn-export {
    background: #059669;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-export:hover {
    background: #047857;
}

.btn-filter {
    background: #d97706;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    border: none;
}

.btn-filter:hover {
    background: #b45309;
}

.btn-clear {
    background: #f1f5f9;
    color: #334155;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    text-decoration: none;
}

.btn-clear:hover {
    background: #e2e8f0;
}

.form-control {
    border-radius: 8px;
    font-size: 0.85rem;
}

.form-control:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 2px rgba(245,158,11,0.15);
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
                    <i class="fas fa-chalkboard-user me-2"></i>Lecturers Report
                </h1>
                <p class="mb-0 text-white-50 small">
                    Faculty members and their teaching assignments
                </p>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION - Department field removed -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Report</h5>
        <form method="GET" action="{{ route('admin.reports.lecturers') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small fw-bold">Search Lecturer</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Joined After</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', $dateFrom) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Apply
                </button>
                <a href="{{ route('admin.reports.lecturers') }}" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-label">Total Lecturers</div>
            <div class="stat-value">{{ $data->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Units Taught</div>
            <div class="stat-value">{{ $data->sum('units_count') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Avg Units per Lecturer</div>
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

    <!-- Export -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.reports.export-csv', [
            'type' => 'lecturers',
            'from' => request('from', $dateFrom),
            'to' => request('to', $dateTo),
            'search' => request('search')
        ]) }}" class="btn-export">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <!-- Grid -->
    <div class="lecturers-grid">
        @forelse($data as $lecturer)
            <div class="lecturer-card">
                <div class="lecturer-avatar">
                    {{ strtoupper(substr($lecturer->name, 0, 2)) }}
                </div>
                <div class="lecturer-name">{{ $lecturer->name }}</div>
                <div class="lecturer-email">{{ $lecturer->email }}</div>
                <div class="mt-3 pt-2 border-top d-flex justify-content-center gap-2">
                    <span class="badge-soft">
                        {{ $lecturer->units_count }} Units
                    </span>
                    <span class="badge-muted">
                        {{ $lecturer->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No lecturers found for the selected filters.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection