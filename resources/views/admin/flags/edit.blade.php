@extends('admin.layouts.master')

@section('title', 'Flag Details')
@section('page-icon', 'fa-flag')
@section('page-title', 'Flag Details')

@push('styles')
<style>
    .flag-badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    .flag-pending { background: #fff3cd; color: #856404; }
    .flag-reviewed { background: #cce5ff; color: #004085; }
    .flag-resolved { background: #d4edda; color: #155724; }
    .flag-dismissed { background: #e2e3e5; color: #383d41; }

    .reason-badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    .reason-spam { background: #f8d7da; color: #721c24; }
    .reason-harassment { background: #fff3cd; color: #856404; }
    .reason-inappropriate { background: #d1ecf1; color: #0c5460; }
    .reason-offensive { background: #d4edda; color: #155724; }
    .reason-other { background: #e2e3e5; color: #383d41; }

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

    .content-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        font-size: 14px;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Flags', 'url' => '/admin/flags'],
        ['name' => 'Details', 'url' => null],
    ]" />

    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Flag Details Card -->
        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #1e293b;">Flag #{{ $flag->id }}</h2>
                <span class="flag-badge flag-{{ $flag->status }}">
                    {{ ucfirst($flag->status) }}
                </span>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-user"></i> Reported By</div>
                <div class="detail-value">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            {{ substr($flag->reporter->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 500;">{{ $flag->reporter->name ?? 'Unknown' }}</div>
                            <div style="font-size: 13px; color: #64748b;">{{ $flag->reporter->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-user-slash"></i> Reported User</div>
                <div class="detail-value">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; background: #fecaca; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #b91c1c;">
                            {{ substr($flag->reportedUser->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 500;">{{ $flag->reportedUser->name ?? 'Unknown' }}</div>
                            <div style="font-size: 13px; color: #64748b;">{{ $flag->reportedUser->email ?? '' }}</div>
                            @if($flag->reportedUser)
                                <div style="margin-top: 5px;">
                                    <span class="flag-badge flag-pending">
                                        <i class="fas fa-flag"></i> {{ $flag->reportedUser->pending_flags_count }} pending
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-tag"></i> Reason</div>
                <div class="detail-value">
                    <span class="reason-badge reason-{{ $flag->reason }}">
                        {{ ucfirst($flag->reason) }}
                    </span>
                </div>
            </div>

            @if($flag->description)
            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                <div class="detail-value">
                    <div class="content-box">
                        {{ $flag->description }}
                    </div>
                </div>
            </div>
            @endif

            @if($flag->forumPost || $flag->forumReply)
            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-file"></i> Reported Content</div>
                <div class="detail-value">
                    @if($flag->forumPost)
                        <div class="content-box">
                            <div style="font-weight: 600; margin-bottom: 10px;"><i class="fas fa-file-alt"></i> Forum Post: {{ $flag->forumPost->title }}</div>
                            <div style="color: #475569;">{{ $flag->forumPost->content }}</div>
                            <div style="margin-top: 10px; font-size: 12px; color: #64748b;">
                                <i class="fas fa-calendar-alt"></i> Posted: {{ $flag->forumPost->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    @elseif($flag->forumReply)
                        <div class="content-box">
                            <div style="font-weight: 600; margin-bottom: 10px;"><i class="fas fa-reply"></i> Forum Reply</div>
                            <div style="color: #475569;">{{ $flag->forumReply->content }}</div>
                            <div style="margin-top: 10px; font-size: 12px; color: #64748b;">
                                <i class="fas fa-calendar-alt"></i> Replied: {{ $flag->forumReply->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-clock"></i> Reported On</div>
                <div class="detail-value">
                    {{ $flag->created_at->format('F d, Y H:i') }}
                </div>
            </div>

            @if($flag->resolved_at)
            <div class="detail-row">
                <div class="detail-label"><i class="fas fa-check-circle"></i> Resolved On</div>
                <div class="detail-value">
                    {{ $flag->resolved_at->format('F d, Y H:i') }}
                    @if($flag->resolver)
                        <span style="color: #64748b; font-size: 13px;">by {{ $flag->resolver->name }}</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Action Form -->
        <div class="detail-card">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 20px;">
                <i class="fas fa-gavel"></i> Take Action
            </h3>

            <form action="{{ route('admin.flags.update', $flag) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label"><i class="fas fa-tag"></i> Status</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ $flag->status == 'pending' ? 'selected' : '' }}><i class="fas fa-hourglass-half"></i> Pending</option>
                        <option value="reviewed" {{ $flag->status == 'reviewed' ? 'selected' : '' }}><i class="fas fa-eye"></i> Reviewed</option>
                        <option value="resolved" {{ $flag->status == 'resolved' ? 'selected' : '' }}><i class="fas fa-check-circle"></i> Resolved</option>
                        <option value="dismissed" {{ $flag->status == 'dismissed' ? 'selected' : '' }}><i class="fas fa-times-circle"></i> Dismissed</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label"><i class="fas fa-exclamation-triangle"></i> Action on User</label>
                    <select name="action" class="form-select">
                        <option value="">-- No action --</option>
                        <option value="warn"><i class="fas fa-exclamation"></i> Send Warning</option>
                        <option value="suspend"><i class="fas fa-pause-circle"></i> Suspend (7 days)</option>
                        <option value="ban"><i class="fas fa-ban"></i> Permanent Ban</option>
                        <option value="remove_forum_access"><i class="fas fa-comment-slash"></i> Remove Forum Access</option>
                    </select>
                    <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> Select an action to apply to the reported user.
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label"><i class="fas fa-sticky-note"></i> Admin Notes</label>
                    <textarea name="admin_notes" class="form-textarea" placeholder="Add notes about your decision...">{{ $flag->admin_notes }}</textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Flag
                    </button>
                    <a href="{{ route('admin.flags.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Flags
                    </a>
                </div>
            </form>
        </div>

        <!-- User Flag History -->
        @if($flag->reportedUser && $flag->reportedUser->flagsReceived->count() > 1)
        <div class="detail-card">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">
                <i class="fas fa-history"></i> User Flag History
            </h3>

            @foreach($flag->reportedUser->flagsReceived->where('id', '!=', $flag->id)->take(5) as $oldFlag)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8fafc; border-radius: 5px; margin-bottom: 8px;">
                    <div>
                        <span class="reason-badge reason-{{ $oldFlag->reason }}" style="margin-right: 10px;">
                            {{ ucfirst($oldFlag->reason) }}
                        </span>
                        <span style="font-size: 12px; color: #64748b;">
                            <i class="fas fa-calendar-alt"></i> {{ $oldFlag->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <span class="flag-badge flag-{{ $oldFlag->status }}">
                        {{ ucfirst($oldFlag->status) }}
                    </span>
                </div>
            @endforeach

            @if($flag->reportedUser->flagsReceived->count() > 5)
                <div style="text-align: center; margin-top: 10px;">
                    <span style="color: #64748b; font-size: 12px;">
                        <i class="fas fa-plus-circle"></i> +{{ $flag->reportedUser->flagsReceived->count() - 5 }} more flags
                    </span>
                </div>
            @endif
        </div>
        @endif
    </div>
@endsection
