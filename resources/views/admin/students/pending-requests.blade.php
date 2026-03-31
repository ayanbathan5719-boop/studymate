@extends('admin.layouts.master')

@section('title', 'Pending Enrollment Requests')
@section('page-icon', 'fa-clock')
@section('page-title', 'Pending Enrollment Requests')

@section('content')
<div class="pending-requests-container">
    <div class="header-section">
        <h1><i class="fas fa-clock"></i> Pending Enrollment Requests</h1>
        <p class="subtitle">Review and approve student enrollment requests</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($pendingEnrollments->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>No Pending Requests</h3>
            <p>All enrollment requests have been processed.</p>
        </div>
    @else
        <div class="requests-table-container">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Unit</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingEnrollments as $enrollment)
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="student-avatar">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="student-details">
                                        <strong>{{ $enrollment->student->name }}</strong>
                                        <span class="student-role">Student</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $enrollment->student->email }}</td>
                            <td>
                                <div class="unit-info">
                                    <span class="unit-code">{{ $enrollment->unit->code }}</span>
                                    <span class="unit-name">{{ $enrollment->unit->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="request-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $enrollment->created_at->format('M d, Y') }}
                                    <span class="time-ago">{{ $enrollment->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- UPDATED: Approve button with custom modal -->
                                    <button type="button" 
                                            class="btn-approve" 
                                            onclick="openApproveModal({{ $enrollment->id }}, '{{ addslashes($enrollment->student->name) }}', '{{ $enrollment->unit->code }}')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn-reject" 
                                            onclick="openRejectModal({{ $enrollment->id }}, '{{ addslashes($enrollment->student->name) }}', '{{ $enrollment->unit->code }}')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{ $pendingEnrollments->links() }}
        </div>
    @endif
</div>

{{-- ============================================= --}}
{{-- REJECTION MODAL --}}
{{-- ============================================= --}}
<div class="modal" id="rejectModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header reject-header">
            <h5><i class="fas fa-ban"></i> Reject Enrollment Request</h5>
            <button type="button" class="close-modal" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <p>Are you sure you want to reject this enrollment request?</p>
                <div class="student-info-display" id="rejectModalStudentInfo"></div>
                
                <div class="form-group">
                    <label for="rejection_reason">Reason (Optional)</label>
                    <textarea name="rejection_reason" 
                              id="rejection_reason" 
                              rows="3" 
                              class="form-control"
                              placeholder="Provide a reason for rejection..."></textarea>
                </div>
                <p class="warning-text">This action cannot be undone. The student will be notified.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-confirm-reject">
                    <i class="fas fa-ban"></i> Yes, Reject
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================= --}}
{{-- APPROVE CONFIRMATION MODAL --}}
{{-- ============================================= --}}
<div class="modal" id="approveModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header approve-header">
            <h5><i class="fas fa-check-circle"></i> Approve Enrollment Request</h5>
            <button type="button" class="close-modal" onclick="closeApproveModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to approve this enrollment request?</p>
            <div class="student-info-display" id="approveModalStudentInfo"></div>
            <p class="success-text">
                <i class="fas fa-info-circle"></i> 
                The student will be notified and will gain immediate access to the unit.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeApproveModal()">Cancel</button>
            <form id="approveForm" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-confirm-approve">
                    <i class="fas fa-check"></i> Yes, Approve
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pending-requests-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

.header-section {
    margin-bottom: 32px;
}

.header-section h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.header-section h1 i {
    color: #f59e0b;
    background: #fffbeb;
    padding: 8px;
    border-radius: 14px;
    font-size: 1.5rem;
}

.header-section .subtitle {
    color: #64748b;
    font-size: 1rem;
    margin-left: 52px;
}

.alert-success,
.alert-error {
    padding: 16px 20px;
    border-radius: 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    color: #166534;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 24px;
    border: 2px dashed #e2e8f0;
}

.empty-icon i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.empty-state p {
    color: #64748b;
}

.requests-table-container {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.requests-table {
    width: 100%;
    border-collapse: collapse;
}

.requests-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.requests-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.requests-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

.requests-table tr:last-child td {
    border-bottom: none;
}

.requests-table tr:hover {
    background: #f8fafc;
}

.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f59e0b;
}

.student-details {
    display: flex;
    flex-direction: column;
}

.student-details strong {
    color: #0f172a;
    font-size: 0.95rem;
}

.student-role {
    font-size: 0.7rem;
    color: #64748b;
}

.unit-info {
    display: flex;
    flex-direction: column;
}

.unit-code {
    font-size: 0.75rem;
    font-weight: 600;
    color: #f59e0b;
    background: #fffbeb;
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-block;
    width: fit-content;
    margin-bottom: 4px;
}

.unit-name {
    font-size: 0.85rem;
    color: #334155;
}

.request-date {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.request-date i {
    color: #94a3b8;
    font-size: 0.7rem;
    margin-right: 4px;
}

.time-ago {
    font-size: 0.7rem;
    color: #94a3b8;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn-approve,
.btn-reject {
    padding: 8px 16px;
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.pagination-container {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 24px;
    width: 100%;
    max-width: 450px;
    overflow: hidden;
    animation: slideUp 0.2s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header.reject-header {
    background: #fef2f2;
    border-bottom: 1px solid #fee2e2;
}

.modal-header.approve-header {
    background: #f0fdf4;
    border-bottom: 1px solid #dcfce7;
}

.modal-header h5 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-header.reject-header h5 {
    color: #991b1b;
}

.modal-header.approve-header h5 {
    color: #166534;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
    transition: color 0.2s;
}

.close-modal:hover {
    color: #0f172a;
}

.modal-body {
    padding: 24px;
}

.student-info-display {
    background: #f8fafc;
    padding: 12px;
    border-radius: 12px;
    margin: 12px 0;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
}

.form-group {
    margin-top: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #334155;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9rem;
    resize: vertical;
    font-family: inherit;
}

.form-control:focus {
    border-color: #f59e0b;
    outline: none;
}

.warning-text {
    font-size: 0.8rem;
    color: #ef4444;
    margin-top: 12px;
    padding: 8px;
    background: #fef2f2;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.success-text {
    color: #10b981;
    font-size: 0.85rem;
    margin-top: 12px;
    padding: 8px;
    background: #f0fdf4;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.modal-footer {
    padding: 20px 24px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-cancel {
    padding: 8px 20px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

.btn-confirm-reject {
    padding: 8px 20px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-confirm-reject:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-confirm-approve {
    padding: 8px 20px;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-confirm-approve:hover {
    background: #059669;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .pending-requests-container {
        padding: 16px;
    }
    
    .requests-table {
        display: block;
        overflow-x: auto;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-approve,
    .btn-reject {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// =============================================
// REJECT MODAL FUNCTIONS
// =============================================
function openRejectModal(enrollmentId, studentName, unitCode) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const studentInfo = document.getElementById('rejectModalStudentInfo');
    
    form.action = `/admin/students/enrollments/${enrollmentId}/reject`;
    studentInfo.innerHTML = `<strong>Student:</strong> ${escapeHtml(studentName)}<br><strong>Unit:</strong> ${escapeHtml(unitCode)}`;
    
    modal.style.display = 'flex';
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'none';
    document.getElementById('rejection_reason').value = '';
}

// =============================================
// APPROVE MODAL FUNCTIONS
// =============================================
function openApproveModal(enrollmentId, studentName, unitCode) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const studentInfo = document.getElementById('approveModalStudentInfo');
    
    form.action = `/admin/students/enrollments/${enrollmentId}/approve`;
    studentInfo.innerHTML = `<strong>Student:</strong> ${escapeHtml(studentName)}<br><strong>Unit:</strong> ${escapeHtml(unitCode)}`;
    
    modal.style.display = 'flex';
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.style.display = 'none';
}

// =============================================
// HELPER FUNCTIONS
// =============================================
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const rejectModal = document.getElementById('rejectModal');
    const approveModal = document.getElementById('approveModal');
    
    if (e.target === rejectModal) {
        closeRejectModal();
    }
    if (e.target === approveModal) {
        closeApproveModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeApproveModal();
    }
});
</script>
@endpush