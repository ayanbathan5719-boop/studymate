@extends('admin.layouts.master')

@section('title', 'Audit Log Details')
@section('page-icon', 'fa-file-alt')
@section('page-title', 'Log Details')

@push('styles')
<style>
    .log-badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .log-CREATE { background: #d4edda; color: #155724; }
    .log-UPDATE { background: #fff3cd; color: #856404; }
    .log-DELETE { background: #f8d7da; color: #721c24; }
    .log-LOGIN { background: #cce5ff; color: #004085; }
    .log-LOGOUT { background: #e2e3e5; color: #383d41; }
    .log-DENY_ACCESS { background: #f8d7da; color: #721c24; }
    .log-RESTORE_ACCESS { background: #d4edda; color: #155724; }
    .log-CONFIGURE { background: #d1ecf1; color: #0c5460; }
    .log-GENERATE_REPORT { background: #d6d8d9; color: #1e1e2f; }
    
    .detail-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .detail-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .detail-label {
        width: 150px;
        font-weight: 600;
        color: #475569;
    }
    
    .detail-value {
        flex: 1;
        color: #1e293b;
    }
    
    .data-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        font-family: monospace;
        font-size: 13px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .data-box pre {
        margin: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Audit Logs', 'url' => '/admin/audit'],
        ['name' => 'Details', 'url' => null],
    ]" />

    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Log Details Card -->
        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #1e293b;">
                    <i class="fas fa-file-alt"></i> Audit Log #{{ $log->id }}
                </h2>
                <span class="log-badge log-{{ $log->action }}">
                    {{ $log->action }}
                </span>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-user"></i> User</div>
                <div class="detail-value">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 500;">{{ $log->user->name ?? 'System' }}</div>
                            <div style="font-size: 13px; color: #64748b;">{{ $log->user->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-tag"></i> Action</div>
                <div class="detail-value">
                    <span class="log-badge log-{{ $log->action }}">
                        {{ $log->action }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-cube"></i> Module</div>
                <div class="detail-value">
                    <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 20px; font-size: 12px;">
                        {{ $log->module }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                <div class="detail-value">
                    {{ $log->description }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-network-wired"></i> IP Address</div>
                <div class="detail-value">
                    <span style="font-family: monospace;">{{ $log->ip_address }}</span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-globe"></i> User Agent</div>
                <div class="detail-value">
                    <span style="font-size: 13px; color: #64748b;">{{ $log->user_agent ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-clock"></i> Timestamp</div>
                <div class="detail-value">
                    {{ $log->created_at->format('F d, Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Data Changes Card -->
        @if($log->old_data || $log->new_data)
        <div class="detail-card">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                <i class="fas fa-code-branch"></i> Data Changes
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @if($log->old_data)
                <div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #b91c1c; margin-bottom: 10px;">
                        <i class="fas fa-history"></i> Old Data
                    </h4>
                    <div class="data-box">
                        <pre>{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
                
                @if($log->new_data)
                <div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #10b981; margin-bottom: 10px;">
                        <i class="fas fa-file"></i> New Data
                    </h4>
                    <div class="data-box">
                        <pre>{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>
@endsection