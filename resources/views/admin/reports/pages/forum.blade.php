@extends('admin.layouts.master')

@section('title', 'Forum Activity Report')
@section('page-icon', 'fa-comments')
@section('page-title', 'Forum Activity Report')

@push('styles')
<style>
.report-header {
    background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
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

.forum-posts {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.forum-post-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px;
    border: 1px solid #e5e7eb;
    transition: all 0.25s ease;
}

.forum-post-card:hover {
    transform: translateX(4px);
    border-color: #db2777;
    box-shadow: 0 8px 18px rgba(0,0,0,0.06);
}

.post-title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.post-meta {
    font-size: 0.8rem;
    color: #64748b;
}

.badge-soft-high {
    background: #fee2e2;
    color: #991b1b;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
}

.badge-soft-medium {
    background: #fef3c7;
    color: #92400e;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
}

.badge-soft-low {
    background: #dbeafe;
    color: #1e40af;
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
    background: #db2777;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    border: none;
}

.btn-filter:hover {
    background: #be185d;
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
    border-color: #ec489a;
    box-shadow: 0 0 0 2px rgba(236,72,153,0.15);
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
                    <i class="fas fa-comments me-2"></i>Forum Activity Report
                </h1>
                <p class="mb-0 text-white-50 small">
                    Overview of all forum discussions and engagement
                </p>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Report</h5>

        <form method="GET" action="{{ route('admin.reports.forum') }}" class="row g-3">

            <div class="col-md-3">
                <label class="form-label small fw-bold">Search Posts</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Title or content..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Unit</label>
                <select name="unit" class="form-control">
                    <option value="">All Units</option>
                    @foreach(\App\Models\Unit::orderBy('code')->get() as $unit)
                        <option value="{{ $unit->code }}" {{ request('unit') == $unit->code ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Author</label>
                <select name="author" class="form-control">
                    <option value="">All Authors</option>
                    @foreach(\App\Models\User::whereHas('forumPosts')->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('author') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->hasRole('lecturer') ? 'Lecturer' : 'Student' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.reports.forum') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                </a>
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

            <div class="col-md-2">
                <label class="form-label small fw-bold">Min Replies</label>
                <input type="number" name="min_replies" class="form-control"
                       placeholder="Minimum replies"
                       value="{{ request('min_replies') }}">
            </div>

        </form>
    </div>

    <!-- Stats -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-label">Total Posts</div>
            <div class="stat-value">{{ $data->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Replies</div>
            <div class="stat-value">{{ $data->sum('replies_count') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Views</div>
            <div class="stat-value">{{ $data->sum('views') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Avg Replies per Post</div>
            <div class="stat-value">
                {{ $data->avg('replies_count') ? round($data->avg('replies_count'), 1) : 0 }}
            </div>
        </div>
    </div>

    <!-- Export -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.reports.export-csv', [
            'type' => 'forum',
            'from' => request('from', $dateFrom),
            'to' => request('to', $dateTo),
            'search' => request('search'),
            'unit' => request('unit'),
            'author' => request('author'),
            'min_replies' => request('min_replies')
        ]) }}" class="btn-export">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <!-- Posts -->
    <div class="forum-posts">
        @forelse($data as $post)
            @php
                $level = $post->replies_count >= 10 ? 'high' : ($post->replies_count >= 3 ? 'medium' : 'low');
            @endphp

            <div class="forum-post-card">

                <div class="d-flex justify-content-between align-items-start">
                    <div class="post-title">
                        {{ \Illuminate\Support\Str::limit($post->title, 60) }}
                    </div>

                    <span class="
                        {{ $level == 'high' ? 'badge-soft-high' : ($level == 'medium' ? 'badge-soft-medium' : 'badge-soft-low') }}
                    ">
                        {{ $level == 'high' ? 'High Activity' : ($level == 'medium' ? 'Medium Activity' : 'Low Activity') }}
                    </span>
                </div>

                <div class="post-meta mt-1">
                    <i class="fas fa-user"></i> {{ $post->user->name ?? 'Unknown' }}
                    • {{ $post->unit->name ?? 'Unknown Unit' }}
                </div>

                <div class="mt-2 small text-muted d-flex gap-3">
                    <span><i class="fas fa-reply"></i> {{ $post->replies_count }}</span>
                    <span><i class="fas fa-eye"></i> {{ $post->views }}</span>
                    <span><i class="fas fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</span>
                </div>

            </div>

        @empty
            <div class="empty-state">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">No forum posts found for the selected filters.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection