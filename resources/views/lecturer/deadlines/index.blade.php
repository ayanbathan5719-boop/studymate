@extends('lecturer.layouts.master')
@section('title', 'Deadlines')
@section('page-title', 'Manage Deadlines')

@push('styles')
<style>
    .deadlines-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
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

    .btn-outline {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-outline:hover {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    .btn-edit {
        background: #14b8a6;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        background: #0d9488;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }

    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .success-message {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filters-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        color: #475569;
        font-weight: 500;
    }

    .filter-group label i {
        color: #f59e0b;
        margin-right: 5px;
    }

    .filter-select, .filter-input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .filter-select:focus, .filter-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filter {
        padding: 10px 24px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        background: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .deadlines-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .deadlines-table th {
        background: #f8fafc;
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .deadlines-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }

    .deadlines-table tbody tr {
        transition: all 0.3s ease;
    }

    .deadlines-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .deadlines-table tbody tr:hover td:first-child {
        border-left: 4px solid #f59e0b;
    }

    .deadline-title {
        font-weight: 600;
        color: #1e293b;
    }

    .deadline-unit {
        font-size: 0.9rem;
        color: #64748b;
        margin-top: 4px;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }

    .badge-urgent {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-normal {
        background: #e2e8f0;
        color: #475569;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-expired {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .deadline-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    .pagination-wrapper {
        margin-top: 30px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .deadlines-table {
            display: block;
            overflow-x: auto;
        }
        
        .header-section {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .filters-section {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="deadlines-container">
    <div class="header-section">
        <h1><i class="fas fa-hourglass-half"></i> Deadlines</h1>
        <a href="{{ route('lecturer.deadlines.create') }}" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Deadline
        </a>
    </div>

    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('lecturer.deadlines.index') }}" style="display: contents;">
            <div class="filter-group">
                <label><i class="fas fa-layer-group"></i> Unit</label>
                <select name="unit" class="filter-select">
                    <option value="">All Units</option>
                    @php
                        $units = Auth::user()->units()->get();
                    @endphp
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->code }} - {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Deadlines</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </form>
    </div>

    <!-- Deadlines Table -->
    @if($deadlines->count() > 0)
        <table class="deadlines-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Unit</th>
                    <th>Due Date</th>
                    <th>Points</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deadlines as $deadline)
                    @php
                        $now = now();
                        $dueDate = \Carbon\Carbon::parse($deadline->due_date);
                        
                        if ($dueDate->isPast()) {
                            $status = 'expired';
                            $statusText = 'Expired';
                            $statusClass = 'status-expired';
                        } elseif ($dueDate->diffInDays($now) <= 3) {
                            $status = 'urgent';
                            $statusText = 'Urgent';
                            $statusClass = 'badge-urgent';
                        } elseif ($dueDate->diffInDays($now) <= 7) {
                            $status = 'warning';
                            $statusText = 'Soon';
                            $statusClass = 'badge-warning';
                        } else {
                            $status = 'normal';
                            $statusText = 'Upcoming';
                            $statusClass = 'badge-normal';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="deadline-title">{{ $deadline->title }}</div>
                            @if($deadline->description)
                                <div class="deadline-unit">{{ Str::limit($deadline->description, 50) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-normal">{{ $deadline->unit->code }}</span>
                            <div class="deadline-unit">{{ $deadline->unit->name }}</div>
                        </td>
                        <td>
                            <strong>{{ $dueDate->format('M d, Y') }}</strong><br>
                            <span style="font-size: 0.85rem; color: #64748b;">{{ $dueDate->format('h:i A') }}</span>
                        </td>
                        <td>{{ $deadline->points ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            <div class="deadline-actions">
                                <a href="{{ route('lecturer.deadlines.edit', $deadline) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('lecturer.deadlines.destroy', $deadline) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this deadline?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-hourglass-half"></i>
            <h3>No Deadlines Yet</h3>
            <p>Create your first deadline to keep students on track.</p>
            <a href="{{ route('lecturer.deadlines.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Create First Deadline
            </a>
        </div>
    @endif

    <div class="pagination-wrapper">
        {{ $deadlines->withQueryString()->links() }}
    </div>
</div>
@endsection