@extends('admin.layouts.master')

@section('title', 'Flags Management')
@section('page-icon', 'fa-flag')
@section('page-title', 'Flags')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Flags</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .flags-container {
        padding: 20px 0;
    }
    
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .filter-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
    }
    
    .filter-group label i {
        color: #667eea;
        margin-right: 5px;
    }
    
    .filter-select, .filter-input {
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    
    .filter-select:hover, .filter-input:hover {
        border-color: #cbd5e1;
    }
    
    .filter-select:focus, .filter-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }
    
    .btn-filter {
        padding: 10px 20px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-filter:hover {
        background: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-clear {
        padding: 10px 20px;
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-clear:hover {
        background: #cbd5e1;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        cursor: default;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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
        font-weight: 600;
        color: #475569;
        margin: 0;
    }
    
    .stat-header i {
        font-size: 1.5rem;
        color: #f59e0b;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
    }
    
    .flags-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow-x: auto;
    }
    
    .flags-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .flags-table th {
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
    
    .flags-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }
    
    .flags-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .flags-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        cursor: pointer;
    }
    
    .flags-table tbody tr:hover td:first-child {
        border-left: 4px solid #f59e0b;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-resolved {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-dismissed {
        background: #e2e8f0;
        color: #475569;
    }
    
    .flag-content-preview {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #64748b;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    .btn-resolve {
        background: #10b981;
        color: white;
    }
    
    .btn-resolve:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-dismiss {
        background: #64748b;
        color: white;
    }
    
    .btn-dismiss:hover {
        background: #475569;
        box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
    }
    
    .btn-delete:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
        transition: all 0.3s ease;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .empty-state:hover i {
        color: #10b981;
        transform: scale(1.05);
    }
    
    .empty-state h3 {
        color: #334155;
        font-size: 1.3rem;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #94a3b8;
        margin-bottom: 0;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    }
    
    .modal-icon {
        font-size: 48px;
        color: #f59e0b;
        margin-bottom: 15px;
    }
    
    .modal-title {
        font-size: 1.3rem;
        color: #1e293b;
        margin-bottom: 10px;
    }
    
    .modal-message {
        color: #64748b;
        margin-bottom: 25px;
    }
    
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .modal-btn {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .modal-btn-secondary {
        background: #e2e8f0;
        color: #475569;
    }
    
    .modal-btn-secondary:hover {
        background: #cbd5e1;
    }
    
    .modal-btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .modal-btn-danger:hover {
        background: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="flags-container">
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.flags.index') }}" class="filter-grid">
            <div class="filter-group">
                <label><i class="fas fa-user"></i> User</label>
                <select name="user_id" class="filter-select">
                    <option value="">All Users</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-tag"></i> Status</label>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> From Date</label>
                <input type="date" name="from" class="filter-input" value="{{ request('from') }}">
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> To Date</label>
                <input type="date" name="to" class="filter-input" value="{{ request('to') }}">
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="{{ route('admin.flags.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Total Flags</h3>
                <i class="fas fa-flag"></i>
            </div>
            <div class="stat-value">{{ $totalFlags ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <h3>Pending</h3>
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ $pendingCount ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <h3>Resolved</h3>
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $resolvedCount ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <h3>Dismissed</h3>
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-value">{{ $dismissedCount ?? 0 }}</div>
        </div>
    </div>
    
    <!-- Flags Table -->
    <div class="flags-table-container">
        @if(isset($flags) && $flags->count() > 0)
            <table class="flags-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Flagged Content</th>
                        <th>Reported By</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flags as $flag)
                    <tr>
                        <td>#{{ $flag->id }}</td>
                        <td>
                            <div class="flag-content-preview">
                                {{ Str::limit($flag->content ?? 'No content', 50) }}
                            </div>
                        </td>
                        <td>{{ $flag->reporter->name ?? 'Unknown' }}</td>
                        <td>{{ $flag->reason }}</td>
                        <td>
                            <span class="status-badge status-{{ $flag->status }}">
                                {{ ucfirst($flag->status) }}
                            </span>
                        </td>
                        <td>{{ $flag->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($flag->status == 'pending')
                                    <form method="POST" action="{{ route('admin.flags.update', $flag) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn-action btn-resolve" title="Mark as Resolved">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.flags.update', $flag) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="dismissed">
                                        <button type="submit" class="btn-action btn-dismiss" title="Dismiss">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" 
                                        class="btn-action btn-delete delete-btn" 
                                        data-id="{{ $flag->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-flag"></i>
                <h3>Forum is Clean</h3>
                <p>No flags pending. Everything looks good!</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="modal-title">Delete Flag</h3>
        <p class="modal-message">Are you sure you want to delete this flag? This action cannot be undone.</p>
        <div class="modal-actions">
            <button id="cancelDelete" class="modal-btn modal-btn-secondary">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn modal-btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const cancelDelete = document.getElementById('cancelDelete');
        
        // Delete modal
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const flagId = this.dataset.id;
                deleteForm.action = `/admin/flags/${flagId}`;
                modal.style.display = 'flex';
            });
        });
        
        if (cancelDelete) {
            cancelDelete.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Add hover effect to table rows
        const rows = document.querySelectorAll('.flags-table tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8fafc';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
</script>
@endpush
@endsection