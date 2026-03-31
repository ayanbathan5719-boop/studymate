@extends('admin.layouts.master')

@section('title', 'Flag Management')
@section('page-icon', 'fa-flag')
@section('page-title', 'Flag Management')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">Flags</li>
        </ol>
    </nav>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/forum-flags.css') }}">
@endpush

@section('content')
<div class="flags-container" id="flagsApp">
    <!-- Header -->
    <div class="page-header">
        <h1><i class="fas fa-flag"></i> Flag Management</h1>
        <div class="header-actions">
            <button class="btn-refresh" @click="refreshData">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Pending</h4>
                <div class="stat-value">{{ $flags->where('status', 'pending')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Resolved</h4>
                <div class="stat-value">{{ $flags->where('status', 'resolved')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon dismissed">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Dismissed</h4>
                <div class="stat-value">{{ $flags->where('status', 'dismissed')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-flag"></i>
            </div>
            <div class="stat-info">
                <h4>Total</h4>
                <div class="stat-value">{{ $flags->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" v-if="selectedFlags.length > 0">
        <span class="selected-count">@{{ selectedFlags.length }} flag(s) selected</span>
        <div class="bulk-buttons">
            <button class="btn-bulk-resolve" @click="bulkResolve">
                <i class="fas fa-check-double"></i> Resolve Selected
            </button>
            <button class="btn-bulk-dismiss" @click="bulkDismiss">
                <i class="fas fa-times-double"></i> Dismiss Selected
            </button>
            <button class="btn-bulk-clear" @click="clearSelection">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.forum.flags') }}" class="filters-form" @submit="applyFilters">
            <div class="filter-group">
                <label><i class="fas fa-tag"></i> Status</label>
                <select name="status" v-model="filters.status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="dismissed">Dismissed</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-user"></i> Reported By</label>
                <input type="text" name="reporter" v-model="filters.reporter" class="filter-input" placeholder="Search reporter...">
            </div>

            <div class="filter-group">
                <label><i class="fas fa-flag"></i> Reason</label>
                <input type="text" name="reason" v-model="filters.reason" class="filter-input" placeholder="Search reason...">
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> From</label>
                <input type="date" name="date_from" v-model="filters.date_from" class="filter-input">
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> To</label>
                <input type="date" name="date_to" v-model="filters.date_to" class="filter-input">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="{{ route('admin.forum.flags') }}" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Flags Table -->
    <div class="flags-table-container">
        <table class="flags-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" @change="selectAll" v-model="allSelected">
                    </th>
                    <th>Flag Details</th>
                    <th>Reported Post</th>
                    <th>Reporter</th>
                    <th>Reported User</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flags as $flag)
                    <tr :class="{ 'selected-row': isSelected({{ $flag->id }}) }">
                        <td>
                            <input type="checkbox" 
                                   value="{{ $flag->id }}" 
                                   @change="toggleSelection({{ $flag->id }})"
                                   :checked="isSelected({{ $flag->id }})">
                        </td>
                        <td>
                            <div class="flag-details">
                                <span class="flag-reason">{{ $flag->reason }}</span>
                                @if($flag->moderation_notes)
                                    <span class="mod-notes">
                                        <i class="fas fa-sticky-note"></i> {{ Str::limit($flag->moderation_notes, 50) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.forum.show', $flag->post) }}" class="post-link">
                                {{ Str::limit($flag->post->title ?? 'Deleted Post', 40) }}
                            </a>
                            <div class="post-meta">
                                <span><i class="fas fa-eye"></i> {{ $flag->post->views ?? 0 }}</span>
                                <span><i class="fas fa-comment"></i> {{ $flag->post->replies_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">{{ $flag->reporter->name ?? 'Unknown' }}</span>
                                <span class="user-role {{ $flag->reporter->hasRole('lecturer') ? 'lecturer' : 'student' }}">
                                    {{ $flag->reporter->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">{{ $flag->reportedUser->name ?? 'Unknown' }}</span>
                                <span class="user-role {{ $flag->reportedUser->hasRole('lecturer') ? 'lecturer' : 'student' }}">
                                    {{ $flag->reportedUser->hasRole('lecturer') ? 'Lecturer' : 'Student' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $flag->status }}">
                                {{ ucfirst($flag->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="date-info">
                                <span class="main-date">{{ $flag->created_at->format('M d, Y') }}</span>
                                <span class="time-ago">{{ $flag->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($flag->status === 'pending')
                                    <button class="btn-icon btn-resolve" @click="updateFlag({{ $flag->id }}, 'resolved')" title="Resolve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon btn-dismiss" @click="updateFlag({{ $flag->id }}, 'dismissed')" title="Dismiss">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                <button class="btn-icon btn-view" @click="showNotes({{ $flag->id }})" title="Add Notes">
                                    <i class="fas fa-sticky-note"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-flag"></i>
                            <h3>No Flags Found</h3>
                            <p>There are no flags to display.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $flags->withQueryString()->links() }}
    </div>

    <!-- Notes Modal -->
    <div class="modal" v-if="showNotesModal" @click.self="closeNotesModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-sticky-note"></i> Moderation Notes</h3>
                <button class="btn-close" @click="closeNotesModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" v-model="notesContent" rows="5" placeholder="Add moderation notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" @click="closeNotesModal">Cancel</button>
                <button class="btn-primary" @click="saveNotes">Save Notes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="{{ asset('js/admin/forum-flags.js') }}"></script>
@endpush