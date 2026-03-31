@extends('admin.layouts.master')

@section('title', 'Flags Report')
@section('page-icon', 'fa-flag')
@section('page-title', 'Flags Report')

@push('styles')
<style>
    .report-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
    }

    .filter-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-card.pending { border-top: 4px solid #f59e0b; }
    .stat-card.resolved { border-top: 4px solid #10b981; }
    .stat-card.dismissed { border-top: 4px solid #94a3b8; }
    .stat-card.total { border-top: 4px solid #ef4444; }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
    }

    .flags-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .flag-item {
        background: white;
        border-radius: 14px;
        padding: 18px;
        border-left: 4px solid;
        transition: all 0.25s ease;
    }

    .flag-item:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }

    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-resolved { background: #d1fae5; color: #065f46; }
    .badge-dismissed { background: #f1f5f9; color: #475569; }

    .badge-spam { background: #fee2e2; color: #991b1b; }
    .badge-inappropriate { background: #fef3c7; color: #92400e; }
    .badge-harassment { background: #fecaca; color: #b91c1c; }

    .reason-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .btn-export {
        background: #10b981;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-export:hover { background: #059669; }

    .btn-filter {
        background: #ef4444;
        color: white;
        padding: 10px 24px;
        border-radius: 10px;
        border: none;
    }

    .btn-filter:hover { background: #dc2626; }

    .btn-clear {
        background: #e2e8f0;
        color: #475569;
        padding: 10px 24px;
        border-radius: 10px;
        text-decoration: none;
    }

    .btn-clear:hover {
        background: #cbd5e1;
        color: #1e293b;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="report-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2"><i class="fas fa-flag me-2"></i>Flags Report</h1>
                <p class="mb-0 opacity-75">Moderation overview of flagged content</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Report</h5>

        <form method="GET" action="{{ route('admin.reports.flags') }}" class="row g-3">

            <div class="col-md-3">
                <label class="form-label small fw-bold">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
                    <option value="dismissed" {{ request('status')=='dismissed'?'selected':'' }}>Dismissed</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Reason</label>
                <select name="reason" class="form-control">
                    <option value="">All</option>
                    <option value="spam" {{ request('reason')=='spam'?'selected':'' }}>Spam</option>
                    <option value="inappropriate" {{ request('reason')=='inappropriate'?'selected':'' }}>Inappropriate</option>
                    <option value="harassment" {{ request('reason')=='harassment'?'selected':'' }}>Harassment</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Reported User</label>
                <select name="reported_user" class="form-control">
                    <option value="">All Users</option>
                    @foreach(\App\Models\User::whereHas('receivedFlags')->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('reported_user')==$user->id?'selected':'' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Apply
                </button>
                <a href="{{ route('admin.reports.flags') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                </a>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', $dateFrom) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', $dateTo) }}">
            </div>

        </form>
    </div>

    @php
        $pending = $data->where('status','pending')->count();
        $resolved = $data->where('status','resolved')->count();
        $dismissed = $data->where('status','dismissed')->count();
    @endphp

    <!-- Stats -->
    <div class="stat-cards">
        <div class="stat-card pending">
            <div class="stat-value text-warning">{{ $pending }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card resolved">
            <div class="stat-value text-success">{{ $resolved }}</div>
            <div class="stat-label">Resolved</div>
        </div>
        <div class="stat-card dismissed">
            <div class="stat-value text-secondary">{{ $dismissed }}</div>
            <div class="stat-label">Dismissed</div>
        </div>
        <div class="stat-card total">
            <div class="stat-value text-danger">{{ $data->count() }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>

    <!-- Export -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.reports.export-csv', [
            'type'=>'flags',
            'from'=>request('from',$dateFrom),
            'to'=>request('to',$dateTo),
            'status'=>request('status'),
            'reason'=>request('reason'),
            'reported_user'=>request('reported_user')
        ]) }}" class="btn-export">
            <i class="fas fa-file-csv"></i> Export
        </a>
    </div>

    <!-- List -->
    <div class="flags-list">
        @forelse($data as $flag)

            <div class="flag-item"
                style="border-left-color:
                {{ $flag->status=='pending' ? '#f59e0b' :
                   ($flag->status=='resolved' ? '#10b981' : '#94a3b8') }}">

                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $flag->reporter->name ?? 'Unknown' }}</strong>
                        <span class="text-muted mx-2">→</span>
                        <strong>{{ $flag->reportedUser->name ?? 'Unknown' }}</strong>
                    </div>

                    <span class="badge
                        {{ $flag->status=='pending' ? 'badge-pending' :
                           ($flag->status=='resolved' ? 'badge-resolved' : 'badge-dismissed') }}">
                        {{ ucfirst($flag->status) }}
                    </span>
                </div>

                <div class="mt-2">
                    <span class="reason-tag
                        {{ $flag->reason=='spam' ? 'badge-spam' : 'badge-inappropriate' }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ ucfirst($flag->reason) }}
                    </span>
                </div>

                <div class="mt-2 small text-muted">
                    <i class="fas fa-calendar"></i>
                    {{ $flag->created_at->format('d M Y, H:i') }}
                </div>

                @if($flag->moderation_notes)
                    <div class="mt-2 small bg-light p-2 rounded">
                        {{ $flag->moderation_notes }}
                    </div>
                @endif

            </div>

        @empty
            <div class="empty-state">
                <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                <p class="text-muted">No flags found</p>
            </div>
        @endforelse
    </div>

</div>
@endsection