@extends('admin.layouts.master')

@section('title', 'Audit Logs')
@section('page-icon', 'fa-history')
@section('page-title', 'Audit Logs')

@push('styles')
<style>
    .filter-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .ip-address {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 4px;
        color: #475569;
        display: inline-block;
    }
    
    .timestamp {
        font-size: 12px;
        color: #64748b;
        display: flex;
        flex-direction: column;
    }
    .timestamp-date {
        font-weight: 500;
        color: #334155;
    }
    .timestamp-time {
        font-size: 11px;
        color: #94a3b8;
    }
    
    .action-badge {
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        min-width: 70px;
        text-align: center;
        letter-spacing: 0.3px;
    }
    .action-CREATE { background: #d4edda; color: #155724; border: 1px solid #27ae60; }
    .action-UPDATE { background: #fff3cd; color: #856404; border: 1px solid #f1c40f; }
    .action-DELETE { background: #f8d7da; color: #721c24; border: 1px solid #e74c3c; }
    .action-LOGIN { background: #cce5ff; color: #004085; border: 1px solid #3498db; }
    .action-LOGOUT { background: #e2e3e5; color: #383d41; border: 1px solid #95a5a6; }
    .action-DENY_ACCESS { background: #f8d7da; color: #721c24; border: 1px solid #e74c3c; }
    .action-RESTORE_ACCESS { background: #d4edda; color: #155724; border: 1px solid #27ae60; }
    .action-CONFIGURE { background: #d1ecf1; color: #0c5460; border: 1px solid #3498db; }
    .action-GENERATE_REPORT { background: #e2e3e5; color: #383d41; border: 1px solid #95a5a6; }
    
    .module-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        display: inline-block;
        border: 1px solid #e2e8f0;
    }
    
    .description-cell {
        max-width: 300px;
        color: #334155;
        font-size: 13px;
        line-height: 1.5;
    }
    .description-cell .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .user-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .user-avatar-small {
        width: 28px;
        height: 28px;
        background: #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .view-btn {
        background: #14b8a6;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .view-btn:hover {
        background: #0d9488;
        transform: translateY(-1px);
    }
    
    /* Export Button Styles */
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-export-csv {
        background: #10b981;
        color: white;
    }
    .btn-export-csv:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    .btn-export-pdf {
        background: #dc2626;
        color: white;
    }
    .btn-export-pdf:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    .btn-export.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    .btn-export.disabled:hover {
        transform: none;
        box-shadow: none;
    }
    .btn-filter {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .action-group {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Audit Logs', 'url' => null],
    ]" />

    <!-- Filter Card -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.audit.index') }}">
            <div class="filter-grid">
                <div>
                    <label class="form-label"><i class="fas fa-user"></i> User</label>
                    <select name="user_id" class="form-select">
                        <option value="all">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="form-label"><i class="fas fa-tag"></i> Action</label>
                    <select name="action" class="form-select">
                        <option value="all">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ ($filters['action'] ?? '') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="form-label"><i class="fas fa-cube"></i> Module</label>
                    <select name="module" class="form-select">
                        <option value="all">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ ($filters['module'] ?? '') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="form-label"><i class="fas fa-calendar-alt"></i> From Date</label>
                    <input type="date" name="from" class="form-input" value="{{ $filters['from'] ?? '' }}">
                </div>
                
                <div>
                    <label class="form-label"><i class="fas fa-calendar-alt"></i> To Date</label>
                    <input type="date" name="to" class="form-input" value="{{ $filters['to'] ?? '' }}">
                </div>
                
                <div class="action-group">
                    <button type="submit" class="btn btn-primary btn-filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary btn-filter">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    <a href="{{ route('admin.audit.export') }}?{{ http_build_query(request()->all()) }}" 
                       class="btn-export btn-export-csv">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="#" 
                       class="btn-export btn-export-pdf disabled" 
                       onclick="alert('PDF export will be available in the next update!'); return false;">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #64748b;"><i class="fas fa-file-alt"></i> Total Logs</div>
            <div style="font-size: 24px; font-weight: bold; color: #1e293b;">{{ $logs->total() }}</div>
        </div>
        <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #64748b;"><i class="fas fa-eye"></i> This Page</div>
            <div style="font-size: 24px; font-weight: bold; color: #1e293b;">{{ $logs->count() }}</div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 12%;">User</th>
                    <th style="width: 8%;">Action</th>
                    <th style="width: 8%;">Module</th>
                    <th style="width: 35%;">Description</th>
                    <th style="width: 12%;">IP Address</th>
                    <th style="width: 12%;">Timestamp</th>
                    <th style="width: 8%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="badge badge-secondary">#{{ $log->id }}</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-small">
                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                            </div>
                            <span style="font-weight: 500;">{{ $log->user->name ?? 'System' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="action-badge action-{{ $log->action }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td>
                        <span class="module-badge">
                            {{ $log->module }}
                        </span>
                    </td>
                    <td class="description-cell">
                        <div class="truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-secondary" style="background: #f1f5f9; font-family: monospace;">
                            <i class="fas fa-network-wired"></i> {{ $log->ip_address ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div class="timestamp">
                            <span class="timestamp-date"><i class="fas fa-calendar-day"></i> {{ $log->created_at->format('d M Y') }}</span>
                            <span class="timestamp-time"><i class="fas fa-clock"></i> {{ $log->created_at->format('H:i:s') }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.audit.show', $log) }}" class="view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                        <h3>No Audit Logs Found</h3>
                        <p>No logs match your filter criteria.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div style="margin-top: 30px;">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
@endsection