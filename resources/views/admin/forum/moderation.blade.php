@extends('admin.layouts.master')

@section('title', 'Forum Moderation')
@section('page-icon', 'fa-shield-alt')
@section('page-title', 'Forum Moderation Dashboard')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">Moderation</li>
        </ol>
    </nav>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/forum-moderation.css') }}">
@endpush

@section('content')
<div class="moderation-container" id="moderationApp">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
                <h4>Total Posts</h4>
                <div class="stat-value">{{ $stats['total_posts'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon replies">
                <i class="fas fa-reply"></i>
            </div>
            <div class="stat-info">
                <h4>Total Replies</h4>
                <div class="stat-value">{{ $stats['total_replies'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Pending Flags</h4>
                <div class="stat-value">{{ $stats['pending_flags'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Resolved Flags</h4>
                <div class="stat-value">{{ $stats['resolved_flags'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon dismissed">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Dismissed Flags</h4>
                <div class="stat-value">{{ $stats['dismissed_flags'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pinned">
                <i class="fas fa-thumbtack"></i>
            </div>
            <div class="stat-info">
                <h4>Pinned Posts</h4>
                <div class="stat-value">{{ $stats['pinned_posts'] }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.forum.flags') }}" class="btn-action btn-primary">
            <i class="fas fa-flag"></i> Manage All Flags
        </a>
        <button class="btn-action btn-secondary" @click="refreshData">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    <div class="moderation-grid">
        <!-- Most Flagged Posts -->
        <div class="moderation-card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Most Flagged Posts</h3>
                <span class="badge">{{ $mostFlagged->count() }} posts</span>
            </div>
            <div class="card-body">
                @if($mostFlagged->count() > 0)
                    <div class="flagged-list">
                        @foreach($mostFlagged as $post)
                            <div class="flagged-item">
                                <div class="flagged-content">
                                    <a href="{{ route('admin.forum.show', $post) }}" class="post-title">
                                        {{ Str::limit($post->title, 40) }}
                                    </a>
                                    <div class="post-meta">
                                        <span><i class="fas fa-user"></i> {{ $post->user->name ?? 'Unknown' }}</span>
                                        <span><i class="fas fa-flag"></i> {{ $post->flags_count }} flags</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.forum.show', $post) }}" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No flagged posts at the moment</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Flags -->
        <div class="moderation-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Recent Flags</h3>
                <span class="badge">{{ $recentFlags->count() }} recent</span>
            </div>
            <div class="card-body">
                @if($recentFlags->count() > 0)
                    <div class="flags-list">
                        @foreach($recentFlags as $flag)
                            <div class="flag-item">
                                <div class="flag-content">
                                    <div class="flag-header">
                                        <span class="flag-reporter">
                                            <i class="fas fa-user"></i> {{ $flag->reporter->name ?? 'Unknown' }}
                                        </span>
                                        <span class="flag-date">{{ $flag->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flag-details">
                                        <a href="{{ route('admin.forum.show', $flag->post) }}" class="flagged-post">
                                            {{ Str::limit($flag->post->title ?? 'Deleted Post', 30) }}
                                        </a>
                                        <span class="flag-reason">{{ $flag->reason }}</span>
                                    </div>
                                </div>
                                <div class="flag-actions">
                                    <button class="btn-resolve" @click="resolveFlag({{ $flag->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-dismiss" @click="dismissFlag({{ $flag->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-flag"></i>
                        <p>No recent flags</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Moderation Stats Chart -->
        <div class="moderation-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Flag Statistics</h3>
            </div>
            <div class="card-body">
                <canvas id="flagChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Quick Moderation Actions -->
        <div class="moderation-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-action-grid">
                    <button class="quick-action-btn" @click="bulkResolveFlags">
                        <i class="fas fa-check-double"></i>
                        <span>Resolve All Pending</span>
                    </button>
                    <button class="quick-action-btn" @click="exportModerationLog">
                        <i class="fas fa-file-export"></i>
                        <span>Export Log</span>
                    </button>
                    <button class="quick-action-btn" @click="clearOldFlags">
                        <i class="fas fa-trash-alt"></i>
                        <span>Clear Old Flags</span>
                    </button>
                    <button class="quick-action-btn" @click="generateReport">
                        <i class="fas fa-file-pdf"></i>
                        <span>Generate Report</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    const { createApp, ref, onMounted } = Vue;

    const app = createApp({
        setup() {
            const loading = ref(false);
            const stats = ref(@json($stats));

            const refreshData = async () => {
                loading.value = true;
                try {
                    const response = await fetch('/admin/forum/statistics');
                    const data = await response.json();
                    stats.value = data;
                    updateChart();
                } catch (error) {
                    console.error('Error refreshing data:', error);
                } finally {
                    loading.value = false;
                }
            };

            const resolveFlag = async (flagId) => {
                if (!confirm('Mark this flag as resolved?')) return;
                
                try {
                    const response = await fetch(`/admin/flags/${flagId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: 'resolved' })
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error resolving flag:', error);
                }
            };

            const dismissFlag = async (flagId) => {
                if (!confirm('Dismiss this flag?')) return;
                
                try {
                    const response = await fetch(`/admin/flags/${flagId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: 'dismissed' })
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error dismissing flag:', error);
                }
            };

            const bulkResolveFlags = async () => {
                if (!confirm('Resolve all pending flags?')) return;
                
                try {
                    const response = await fetch('/admin/flags/bulk-update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ action: 'resolve' })
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error bulk resolving flags:', error);
                }
            };

            const exportModerationLog = () => {
                window.location.href = '/admin/forum/export?type=moderation';
            };

            const clearOldFlags = async () => {
                if (!confirm('Clear flags older than 30 days?')) return;
                
                try {
                    const response = await fetch('/admin/flags/clear-old', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error clearing old flags:', error);
                }
            };

            const generateReport = () => {
                window.location.href = '/admin/forum/export?type=report&format=pdf';
            };

            const updateChart = () => {
                const ctx = document.getElementById('flagChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Resolved', 'Dismissed'],
                        datasets: [{
                            data: [stats.value.pending_flags, stats.value.resolved_flags, stats.value.dismissed_flags],
                            backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            };

            onMounted(() => {
                updateChart();
            });

            return {
                loading,
                refreshData,
                resolveFlag,
                dismissFlag,
                bulkResolveFlags,
                exportModerationLog,
                clearOldFlags,
                generateReport
            };
        }
    });

    app.mount('#moderationApp');
</script>
@endpush