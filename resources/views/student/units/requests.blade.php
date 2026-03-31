@extends('student.layouts.master')

@section('title', 'My Enrollment Requests')
@section('page-icon', 'fa-clock')
@section('page-title', 'My Enrollment Requests')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.units.available') }}"><i class="fas fa-layer-group"></i> Available Units</a></li>
        <li class="breadcrumb-item active" aria-current="page">My Requests</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .requests-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    .stat-icon i {
        font-size: 1.5rem;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    .section-header i {
        font-size: 1.3rem;
        color: #f59e0b;
    }
    .section-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1e293b;
    }
    .section-header .badge-count {
        background: #e2e8f0;
        color: #475569;
        padding: 2px 10px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Cards Grid */
    .requests-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .units-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    /* Request Card */
    .request-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .request-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        border-color: #cbd5e1;
    }
    .request-card.pending {
        border-left: 4px solid #f59e0b;
    }
    .request-card.approved {
        border-left: 4px solid #10b981;
    }
    .request-card.rejected {
        border-left: 4px solid #ef4444;
        opacity: 0.9;
    }

    .unit-code {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .unit-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }
    .unit-details {
        display: flex;
        gap: 16px;
        margin: 12px 0;
        font-size: 0.75rem;
        color: #64748b;
    }
    .unit-details span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 12px;
    }
    .status-pending {
        background: #fef3c7;
        color: #b45309;
    }
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .date-info {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-start-learning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-top: 12px;
    }
    .btn-start-learning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        color: white;
    }
    .btn-request-again {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 6px 14px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-request-again:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .rejection-reason {
        margin-top: 12px;
        padding: 8px 12px;
        background: #fef2f2;
        border-radius: 12px;
        font-size: 0.7rem;
        color: #991b1b;
        display: flex;
        align-items: flex-start;
        gap: 6px;
    }
    .rejection-reason i {
        margin-top: 2px;
    }

    .empty-state-small {
        text-align: center;
        padding: 50px 20px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px dashed #cbd5e1;
    }
    .empty-state-small i {
        font-size: 3rem;
        color: #94a3b8;
        margin-bottom: 16px;
    }
    .empty-state-small h4 {
        font-size: 1rem;
        color: #475569;
        margin-bottom: 8px;
    }
    .empty-state-small p {
        font-size: 0.8rem;
        color: #64748b;
    }
    .btn-browse-units {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
        background: #f59e0b;
        color: white;
        padding: 8px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-browse-units:hover {
        background: #d97706;
        transform: translateY(-1px);
        color: white;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #dcfce7;
        color: #166534;
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    @media (max-width: 768px) {
        .requests-container {
            padding: 16px;
        }
        .requests-grid,
        .units-grid {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .stat-card {
            padding: 12px;
        }
        .stat-number {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="requests-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">
                <i class="fas fa-hourglass-half" style="color: #f59e0b;"></i>
            </div>
            <div class="stat-number">{{ $pendingRequests->count() }}</div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5;">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
            </div>
            <div class="stat-number">{{ $approvedUnits->count() }}</div>
            <div class="stat-label">Approved Units</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fee2e2;">
                <i class="fas fa-times-circle" style="color: #ef4444;"></i>
            </div>
            <div class="stat-number">{{ $rejectedRequests->count() }}</div>
            <div class="stat-label">Rejected Requests</div>
        </div>
    </div>

    <!-- Pending Requests Section -->
    @if($pendingRequests->count() > 0)
    <div class="section-header">
        <i class="fas fa-hourglass-half"></i>
        <h3>Pending Requests</h3>
        <span class="badge-count">{{ $pendingRequests->count() }} pending</span>
    </div>
    <div class="requests-grid">
        @foreach($pendingRequests as $request)
        <div class="request-card pending">
            <div class="unit-code">{{ $request->unit->code ?? 'N/A' }}</div>
            <div class="unit-name">{{ $request->unit->name ?? 'Unknown Unit' }}</div>
            <div class="unit-details">
                <span><i class="fas fa-calendar-alt"></i> Requested: {{ $request->created_at->format('M d, Y') }}</span>
                <span><i class="fas fa-clock"></i> {{ $request->created_at->diffForHumans() }}</span>
            </div>
            <div class="status-badge status-pending">
                <i class="fas fa-clock"></i> Pending Approval
            </div>
            <div class="date-info">
                <span><i class="fas fa-spinner fa-spin"></i> Awaiting admin review</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Approved Units Section -->
    @if($approvedUnits->count() > 0)
    <div class="section-header">
        <i class="fas fa-check-circle"></i>
        <h3>Approved Units</h3>
        <span class="badge-count">{{ $approvedUnits->count() }} units</span>
    </div>
    <div class="units-grid">
        @foreach($approvedUnits as $enrollment)
        <div class="request-card approved">
            <div class="unit-code">{{ $enrollment->unit->code ?? 'N/A' }}</div>
            <div class="unit-name">{{ $enrollment->unit->name ?? 'Unknown Unit' }}</div>
            <div class="unit-details">
                <span><i class="fas fa-calendar-check"></i> Approved: {{ $enrollment->approved_at ? $enrollment->approved_at->format('M d, Y') : $enrollment->updated_at->format('M d, Y') }}</span>
            </div>
            <div class="status-badge status-approved">
                <i class="fas fa-check-circle"></i> Approved
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('student.units.show', $enrollment->unit_id) }}" class="btn-start-learning">
                    <i class="fas fa-graduation-cap"></i> Start Learning
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Rejected Requests Section -->
    @if($rejectedRequests->count() > 0)
    <div class="section-header">
        <i class="fas fa-times-circle"></i>
        <h3>Rejected Requests</h3>
        <span class="badge-count">{{ $rejectedRequests->count() }} rejected</span>
    </div>
    <div class="requests-grid">
        @foreach($rejectedRequests as $request)
        <div class="request-card rejected">
            <div class="unit-code">{{ $request->unit->code ?? 'N/A' }}</div>
            <div class="unit-name">{{ $request->unit->name ?? 'Unknown Unit' }}</div>
            <div class="unit-details">
                <span><i class="fas fa-calendar-alt"></i> Requested: {{ $request->created_at->format('M d, Y') }}</span>
            </div>
            @if($request->rejected_reason)
            <div class="rejection-reason">
                <i class="fas fa-info-circle"></i>
                <span><strong>Reason:</strong> {{ $request->rejected_reason }}</span>
            </div>
            @endif
            <div class="status-badge status-rejected">
                <i class="fas fa-times-circle"></i> Not Approved
            </div>
            <div class="date-info">
                <span><i class="fas fa-calendar-alt"></i> Rejected: {{ $request->updated_at->format('M d, Y') }}</span>
                <a href="{{ route('student.units.available') }}" class="btn-request-again">
                    <i class="fas fa-redo-alt"></i> Request Again
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Empty State - No Requests -->
    @if($pendingRequests->count() == 0 && $approvedUnits->count() == 0 && $rejectedRequests->count() == 0)
    <div class="empty-state-small">
        <i class="fas fa-inbox"></i>
        <h4>No Enrollment Requests Yet</h4>
        <p>You haven't requested any unit enrollments yet.</p>
        <a href="{{ route('student.units.available') }}" class="btn-browse-units">
            <i class="fas fa-book"></i> Browse Available Units
        </a>
    </div>
    @endif

    <!-- Quick Action: Browse More Units -->
    @if($pendingRequests->count() > 0 || $approvedUnits->count() > 0)
    <div class="text-center mt-4 pt-3">
        <a href="{{ route('student.units.available') }}" class="btn-browse-units" style="background: #475569;">
            <i class="fas fa-plus-circle"></i> Request More Units
        </a>
    </div>
    @endif
</div>
@endsection