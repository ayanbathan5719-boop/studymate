@extends(Auth::user()->hasRole('admin') ? 'admin.layouts.master' : (Auth::user()->hasRole('lecturer') ? 'lecturer.layouts.master' : 'student.layouts.master'))

@section('title', 'Notifications')
@section('page-icon', 'fa-bell')
@section('page-title', 'Notifications')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/{{ Auth::user()->hasRole('admin') ? 'admin' : (Auth::user()->hasRole('lecturer') ? 'lecturer' : 'student') }}/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ol>
    </nav>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('content')
<div class="notifications-container" id="notificationsApp">
    <!-- Header -->
    <div class="notifications-header">
        <h1><i class="fas fa-bell"></i> Notifications</h1>
        <div class="header-actions">
            <button class="btn-mark-read" @click="markAllAsRead" :disabled="unreadCount === 0">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
            <button class="btn-refresh" @click="refreshNotifications">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('notifications.index') }}" class="filters-form">
            <div class="filter-group">
                <label><i class="fas fa-tag"></i> Type</label>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="forum_reply" {{ ($filters['type'] ?? '') == 'forum_reply' ? 'selected' : '' }}>Forum Replies</option>
                    <option value="post_flagged" {{ ($filters['type'] ?? '') == 'post_flagged' ? 'selected' : '' }}>Flags</option>
                    <option value="post_pinned" {{ ($filters['type'] ?? '') == 'post_pinned' ? 'selected' : '' }}>Pinned Posts</option>
                    <option value="new_forum_post" {{ ($filters['type'] ?? '') == 'new_forum_post' ? 'selected' : '' }}>New Posts</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-circle"></i> Status</label>
                <select name="status" class="filter-select">
                    <option value="">All</option>
                    <option value="unread" {{ ($filters['status'] ?? '') == 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ ($filters['status'] ?? '') == 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Apply
            </button>
            <a href="{{ route('notifications.index') }}" class="btn-clear">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($grouped as $date => $dateNotifications)
            <div class="date-group">
                <div class="date-header">
                    <span class="date-badge">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</span>
                    @if(\Carbon\Carbon::parse($date)->isToday())
                        <span class="today-badge">Today</span>
                    @elseif(\Carbon\Carbon::parse($date)->isYesterday())
                        <span class="yesterday-badge">Yesterday</span>
                    @endif
                </div>

                @foreach($dateNotifications as $notification)
                    <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}"
                         data-id="{{ $notification->id }}"
                         :class="{ 'selected': selectedNotification === '{{ $notification->id }}' }">

                        <div class="notification-checkbox">
                            <input type="checkbox"
                                   value="{{ $notification->id }}"
                                   @change="toggleSelection('{{ $notification->id }}')"
                                   :checked="isSelected('{{ $notification->id }}')">
                        </div>

                        <div class="notification-icon" style="background-color: {{ $notification->data['color'] ?? '#f59e0b' }}20; color: {{ $notification->data['color'] ?? '#f59e0b' }}">
                            <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                        </div>

                        <div class="notification-content" @click="viewNotification('{{ $notification->id }}', '{{ $notification->data['link'] ?? '#' }}')">
                            <div class="notification-header">
                                <h4>{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="notification-message">{{ $notification->data['message'] ?? 'You have a new notification' }}</p>

                            @if(isset($notification->data['preview']))
                                <div class="notification-preview">
                                    "{{ $notification->data['preview'] }}"
                                </div>
                            @endif

                            <div class="notification-meta">
                                @if(isset($notification->data['unit_code']))
                                    <span class="unit-badge">{{ $notification->data['unit_code'] }}</span>
                                @endif
                                @if(!$notification->read_at)
                                    <span class="unread-dot"></span>
                                @endif
                            </div>
                        </div>

                        <div class="notification-actions">
                            <button class="btn-mark-single" @click="markAsRead('{{ $notification->id }}')" title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-delete" @click="deleteNotification('{{ $notification->id }}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications</h3>
                <p>You don't have any notifications at the moment.</p>
                <a href="/{{ Auth::user()->hasRole('admin') ? 'admin' : (Auth::user()->hasRole('lecturer') ? 'lecturer' : 'student') }}/dashboard" class="btn-home">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pagination-wrapper">
            {{ $notifications->withQueryString()->links() }}
        </div>
    @endif

    <!-- Notification Preferences Modal -->
    <div class="modal" v-if="showPreferences" @click.self="closePreferences">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-cog"></i> Notification Preferences</h3>
                <button class="btn-close" @click="closePreferences">&times;</button>
            </div>
            <div class="modal-body">
                <div class="preferences-section">
                    <h4>Email Notifications</h4>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.email_replies">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">Replies to my posts</span>
                            <span class="preference-desc">Receive email when someone replies to your forum posts</span>
                        </div>
                    </div>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.email_new_posts">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">New posts in my units</span>
                            <span class="preference-desc">Receive email when new posts are created in your units</span>
                        </div>
                    </div>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.email_flags">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">Flag notifications</span>
                            <span class="preference-desc">Receive email when posts are flagged (admins only)</span>
                        </div>
                    </div>
                </div>

                <div class="preferences-section">
                    <h4>Push Notifications</h4>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.push_replies">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">Replies to my posts</span>
                            <span class="preference-desc">Receive in-app notifications when someone replies</span>
                        </div>
                    </div>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.push_new_posts">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">New posts in my units</span>
                            <span class="preference-desc">Receive in-app notifications for new posts</span>
                        </div>
                    </div>
                    <div class="preference-item">
                        <label class="switch">
                            <input type="checkbox" v-model="preferences.push_flags">
                            <span class="slider round"></span>
                        </label>
                        <div class="preference-info">
                            <span class="preference-title">Flag notifications</span>
                            <span class="preference-desc">Receive in-app notifications for flags</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" @click="closePreferences">Cancel</button>
                <button class="btn-primary" @click="savePreferences">Save Preferences</button>
            </div>
        </div>
    </div>

    <!-- Settings Button -->
    <button class="btn-settings" @click="openPreferences">
        <i class="fas fa-cog"></i>
    </button>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="{{ asset('js/notifications.js') }}"></script>
@endpush
