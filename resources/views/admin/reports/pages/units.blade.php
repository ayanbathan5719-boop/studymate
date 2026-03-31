@extends('admin.layouts.master')

@section('title', 'Units Report')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'Units Report')

@push('styles')
<style>
.report-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
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

.units-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.unit-item {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px;
    border: 1px solid #e5e7eb;
    transition: all 0.25s ease;
}

.unit-item:hover {
    transform: translateX(4px);
    border-color: #059669;
    box-shadow: 0 8px 18px rgba(0,0,0,0.06);
}

.unit-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #0f172a;
}

.unit-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.badge-active {
    background: #dcfce7;
    color: #166534;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 500;
}

.progress-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
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
    transition: 0.2s;
}

.btn-export:hover {
    background: #047857;
}

.btn-filter {
    background: #059669;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    border: none;
}

.btn-filter:hover {
    background: #047857;
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
    border-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16,185,129,0.15);
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
                    <i class="fas fa-layer-group me-2"></i>Units Report
                </h1>
                <p class="mb-0 text-white-50 small">
                    Detailed breakdown of all units with activity metrics
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

        <form method="GET" action="{{ route('admin.reports.units') }}" class="row g-3">

            <div class="col-md-4">
                <label class="form-label small fw-bold">Search Unit</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Unit name or code..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Course</label>
                <select name="course" class="form-control">
                    <option value="">All Courses</option>
                    @foreach(\App\Models\Course::all() as $course)
                        <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
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
                <a href="{{ route('admin.reports.units') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Statistics -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-label">Total Units</div>
            <div class="stat-value">{{ $data->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Resources</div>
            <div class="stat-value">{{ $data->sum('resources_count') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Forum Posts</div>
            <div class="stat-value">{{ $data->sum('forum_posts_count') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Assigned Lecturers</div>
            <div class="stat-value">{{ $data->whereNotNull('lecturer_id')->count() }}</div>
        </div>
    </div>

    <!-- Export -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.reports.export-csv', [
            'type' => 'units',
            'from' => request('from', $dateFrom),
            'to' => request('to', $dateTo),
            'search' => request('search'),
            'course' => request('course')
        ]) }}" class="btn-export">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <!-- Units List -->
    <div class="units-list">
        @forelse($data as $unit)
            <div class="unit-item">

                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="unit-title">{{ $unit->name }}</div>
                        <div class="unit-meta">
                            {{ $unit->code }} • {{ $unit->course->name ?? 'No Course' }}
                        </div>
                    </div>

                    <span class="badge-active">
                        {{ $unit->lecturer->name ?? 'Unassigned' }}
                    </span>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between small mb-1 text-muted">
                        <span><i class="fas fa-file-alt"></i> {{ $unit->resources_count }} Resources</span>
                        <span><i class="fas fa-comment"></i> {{ $unit->forum_posts_count }} Posts</span>
                        <span>Activity</span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill"
                             style="width: {{ min(100, ($unit->forum_posts_count + $unit->resources_count) * 5) }}%">
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                <p class="text-muted">No units found for the selected filters.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection