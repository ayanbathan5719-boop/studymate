@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')
@section('page-icon', 'fa-chart-pie')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* Professional Dashboard Styles */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        position: relative;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        border-color: #cbd5e1;
    }
    
    .stat-icon {
        font-size: 2rem;
        float: right;
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        opacity: 1;
        transform: scale(1.05);
    }
    
    .stat-label {
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .stat-value {
        color: #1e293b;
        font-size: 2.2rem;
        font-weight: bold;
        margin: 8px 0;
        line-height: 1;
    }
    
    .stat-sub {
        color: #94a3b8;
        font-size: 0.75rem;
    }
    
    /* Chart Containers */
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .chart-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    
    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        display: inline-block;
    }
    
    .chart-title i {
        color: #667eea;
        margin-right: 8px;
    }
    
    .chart-wrapper {
        width: 100%;
        height: 280px;
        position: relative;
    }
    
    /* Unit Cards */
    .unit-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .unit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
        background: white;
    }
    
    .unit-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    
    .unit-name i {
        color: #667eea;
        margin-right: 8px;
    }
    
    .progress-bar {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #667eea;
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    /* Recent Sections */
    .recent-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .recent-section:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        display: inline-block;
    }
    
    .section-title i {
        color: #667eea;
        margin-right: 8px;
    }
    
    .recent-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .recent-item:hover {
        background: white;
        transform: translateX(4px);
        border-color: #cbd5e1;
    }
    
    .recent-avatar {
        width: 40px;
        height: 40px;
        background: #667eea;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .badge-student { background: #dbeafe; color: #1e40af; }
    .badge-lecturer { background: #dcfce7; color: #166534; }
    .badge-admin { background: #fee2e2; color: #991b1b; }
    
    .activity-icon {
        width: 32px;
        height: 32px;
        background: #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #475569;
    }
    
    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card, .chart-container, .recent-section {
        animation: fadeInUp 0.5s ease-out;
    }
</style>
@endpush

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid" id="stats-grid"></div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
        <div class="chart-container">
            <h3 class="chart-title">
                <i class="fas fa-chart-pie"></i> User Distribution
            </h3>
            <div class="chart-wrapper">
                <canvas id="userChart"></canvas>
            </div>
        </div>
        
        <div class="chart-container">
            <h3 class="chart-title">
                <i class="fas fa-chart-line"></i> Weekly Activity
            </h3>
            <div class="chart-wrapper">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Units Section -->
    <div class="chart-container" style="margin-bottom: 32px;">
        <h3 class="chart-title">
            <i class="fas fa-trophy"></i> Most Active Units
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            @foreach($chartData['topUnits'] as $unit)
                <div class="unit-card">
                    <div class="unit-name">
                        <i class="fas fa-layer-group"></i>
                        {{ $unit['name'] }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(100, ($unit['posts'] / max($chartData['topUnits']->max('posts'), 1)) * 100) }}%;"></div>
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 500; color: #667eea;">
                            {{ $unit['posts'] }} posts
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Recent Users -->
        <div class="recent-section">
            <h3 class="section-title">
                <i class="fas fa-user-plus"></i> Newest Members
            </h3>
            <div id="recent-users-list"></div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="recent-section">
            <h3 class="section-title">
                <i class="fas fa-clock"></i> Recent Activity
            </h3>
            <div id="recent-logs-list"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statsData = @json($stats);
        const chartData = @json($chartData);
        
        // Render stats cards with professional colors
        const statsGrid = document.getElementById('stats-grid');
        if (statsGrid) {
            const stats = [
                { label: 'Total Users', value: statsData.total_users, icon: 'fa-users', color: '#3b82f6' },
                { label: 'Students', value: statsData.total_students, icon: 'fa-user-graduate', color: '#10b981' },
                { label: 'Lecturers', value: statsData.total_lecturers, icon: 'fa-chalkboard-user', color: '#f59e0b' },
                { label: 'Courses', value: statsData.total_courses, icon: 'fa-book', color: '#8b5cf6' },
                { label: 'Units', value: statsData.total_units, icon: 'fa-layer-group', color: '#ec489a' },
                { label: 'Forum Posts', value: statsData.total_forum_posts, icon: 'fa-comments', color: '#ef4444' }
            ];
            
            let statsHtml = '';
            stats.forEach((stat) => {
                statsHtml += `
                    <div class="stat-card">
                        <div class="stat-icon" style="color: ${stat.color};">
                            <i class="fas ${stat.icon}"></i>
                        </div>
                        <div class="stat-label">${stat.label}</div>
                        <div class="stat-value">
                            <span class="counter" data-target="${stat.value}">0</span>
                        </div>
                        <div class="stat-sub">total in system</div>
                    </div>
                `;
            });
            statsGrid.innerHTML = statsHtml;
            
            // Animate counters
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target);
                let current = 0;
                const increment = target / 50;
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.textContent = target;
                    }
                };
                updateCounter();
            });
        }
        
        // Render recent users
        const usersList = document.getElementById('recent-users-list');
        if (usersList && statsData.recent_users) {
            let usersHtml = '';
            statsData.recent_users.forEach(user => {
                const initial = user.name ? user.name.charAt(0).toUpperCase() : 'U';
                const roleClass = user.role === 'student' ? 'badge-student' : (user.role === 'lecturer' ? 'badge-lecturer' : 'badge-admin');
                usersHtml += `
                    <div class="recent-item">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="recent-avatar">${initial}</div>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">${escapeHtml(user.name)}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">${escapeHtml(user.email)}</div>
                            </div>
                        </div>
                        <div>
                            <span class="badge ${roleClass}">${escapeHtml(user.role || 'No role')}</span>
                            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;">${user.created_at}</div>
                        </div>
                    </div>
                `;
            });
            usersList.innerHTML = usersHtml;
        }
        
        // Render recent activity
        const logsList = document.getElementById('recent-logs-list');
        if (logsList && statsData.recent_logs) {
            let logsHtml = '';
            statsData.recent_logs.forEach(log => {
                logsHtml += `
                    <div class="recent-item">
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <div class="activity-icon"><i class="fas ${getActionIcon(log.action)}"></i></div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem;">
                                    <strong>${escapeHtml(log.user)}</strong> ${escapeHtml(log.description)}
                                </div>
                                <div style="font-size: 0.7rem; color: #94a3b8;">${log.time}</div>
                            </div>
                        </div>
                        <span class="badge" style="background: ${getActionColor(log.action)}20; color: ${getActionColor(log.action)};">
                            ${log.action}
                        </span>
                    </div>
                `;
            });
            logsList.innerHTML = logsHtml;
        }
        
        function getActionIcon(action) {
            const icons = {
                'LOGIN': 'fa-sign-in-alt',
                'LOGOUT': 'fa-sign-out-alt',
                'CREATE': 'fa-plus-circle',
                'UPDATE': 'fa-edit',
                'DELETE': 'fa-trash-alt',
                'DENY_ACCESS': 'fa-ban',
                'RESTORE_ACCESS': 'fa-check-circle'
            };
            return icons[action] || 'fa-clipboard-list';
        }
        
        function getActionColor(action) {
            const colors = {
                'LOGIN': '#10b981',
                'LOGOUT': '#6b7280',
                'CREATE': '#3b82f6',
                'UPDATE': '#f59e0b',
                'DELETE': '#ef4444',
                'DENY_ACCESS': '#ef4444',
                'RESTORE_ACCESS': '#10b981'
            };
            return colors[action] || '#6b7280';
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialize user distribution chart
        const ctx1 = document.getElementById('userChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Lecturers', 'Admins'],
                datasets: [{
                    data: [
                        chartData.userRoles.Students,
                        chartData.userRoles.Lecturers,
                        chartData.userRoles.Admins
                    ],
                    backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Initialize weekly activity chart
        const ctx2 = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: chartData.weeklyLabels,
                datasets: [
                    {
                        label: 'Forum Posts',
                        data: chartData.weeklyPosts,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Flags',
                        data: chartData.weeklyFlags,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#ef4444'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e2e8f0' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush