@extends('student.layouts.master')

@section('title', 'Available Units')
@section('page-icon', 'fa-book')
@section('page-title', 'Available Units')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Available Units</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .alert-info {
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        color: #1e40af;
        padding: 14px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .request-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f1f5f9;
        padding: 8px 16px;
        border-radius: 10px;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    .request-link:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .course-card {
        background: white;
        border-radius: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    .course-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .course-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 24px;
        color: white;
    }
    .course-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .course-header p {
        margin: 6px 0 0;
        font-size: 0.8rem;
        opacity: 0.9;
    }
    .units-list {
        padding: 20px 24px;
    }
    .unit-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .unit-item:last-child {
        border-bottom: none;
    }
    .unit-info h4 {
        margin: 0 0 6px;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
    .unit-info p {
        margin: 0;
        font-size: 0.75rem;
        color: #64748b;
    }
    .unit-info p i {
        margin-right: 4px;
    }
    .badge-approved {
        background: #d1fae5;
        color: #065f46;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-request {
        background: #dbeafe;
        color: #1e40af;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-request:hover {
        background: #c7d2fe;
        transform: translateY(-1px);
    }
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #667eea;
        margin: 0;
    }
    .btn-enroll {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 40px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-enroll:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    .btn-enroll:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    .empty-state h4 {
        font-size: 1.1rem;
        margin-bottom: 8px;
        color: #64748b;
    }
    .selected-count {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        margin-left: 12px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="alert-info">
        <i class="fas fa-info-circle"></i>
        <span>Browse courses and request enrollment in the units you want to study. Your requests will be reviewed by administrators.</span>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('student.units.requests') }}" class="request-link">
            <i class="fas fa-clock"></i> View My Enrollment Requests
        </a>
    </div>

    <form method="POST" action="{{ route('student.units.enroll') }}" id="enrollForm">
        @csrf
        
        @forelse($courses as $course)
            <div class="course-card">
                <div class="course-header">
                    <h3><i class="fas fa-book-open me-2"></i>{{ $course->name }}</h3>
                    <p>{{ $course->code ?? '' }} • {{ $course->units->count() }} units available</p>
                </div>
                <div class="units-list">
                    @forelse($course->units as $unit)
                        @php
                            $isApproved = in_array($unit->id, $approvedUnitIds ?? []);
                            $isPending = in_array($unit->id, $pendingUnitIds ?? []);
                        @endphp
                        <div class="unit-item">
                            <div class="unit-info">
                                <h4>{{ $unit->name }}</h4>
                                <p>
                                    <i class="fas fa-code"></i> {{ $unit->code }}
                                    <i class="fas fa-calendar-alt ms-2"></i> Created: {{ $unit->created_at->format('M d, Y') }}
                                </p>
                            </div>
                            <div>
                                @if($isApproved)
                                    <span class="badge-approved">
                                        <i class="fas fa-check-circle"></i> Enrolled
                                    </span>
                                @elseif($isPending)
                                    <span class="badge-pending">
                                        <i class="fas fa-clock"></i> Pending Approval
                                    </span>
                                @else
                                    <label class="checkbox-wrapper">
                                        <input type="checkbox" name="unit_ids[]" value="{{ $unit->id }}" class="unit-checkbox" onchange="updateSelectedCount()">
                                        <span class="btn-request">
                                            <i class="fas fa-plus-circle"></i> Request Enrollment
                                        </span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>No units available for this course.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-school"></i>
                <h4>No Courses Available</h4>
                <p>There are no courses available at the moment. Please check back later.</p>
            </div>
        @endforelse

        <div class="text-center mt-4">
            <button type="submit" class="btn-enroll" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Submit Enrollment Requests
                <span id="selectedCount" class="selected-count" style="display: none;">0</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.unit-checkbox:checked');
        const count = checkedBoxes.length;
        const selectedSpan = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');
        
        if (count > 0) {
            selectedSpan.textContent = count;
            selectedSpan.style.display = 'inline-block';
        } else {
            selectedSpan.style.display = 'none';
        }
    }
    
    document.getElementById('enrollForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.unit-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one unit to request enrollment.');
        }
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedCount();
    });
</script>
@endpush