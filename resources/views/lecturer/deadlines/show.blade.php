@extends('lecturer.layouts.master')

@section('title', 'Deadline Details')
@section('page-icon', 'fa-clock')
@section('page-title', 'Deadline Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/lecturer/deadlines">Deadlines</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $deadline->title }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="deadline-show-container">
    <div class="row">
        <div class="col-md-8">
            <!-- Main Deadline Card -->
            <div class="deadline-card">
                <div class="deadline-header">
                    <div class="deadline-title-section">
                        <h1>{{ $deadline->title }}</h1>
                        <span class="deadline-badge {{ $deadline->due_date->isPast() ? 'badge-danger' : 'badge-success' }}">
                            {{ $deadline->due_date->isPast() ? 'Overdue' : 'Active' }}
                        </span>
                    </div>
                    <div class="deadline-meta">
                        <span><i class="fas fa-layer-group"></i> {{ $deadline->unit->code }} - {{ $deadline->unit->name }}</span>
                        <span><i class="fas fa-calendar-alt"></i> Due: {{ $deadline->due_date->format('F d, Y') }}</span>
                        @if($deadline->points)
                            <span><i class="fas fa-star"></i> {{ $deadline->points }} Points</span>
                        @endif
                    </div>
                </div>
                
                <div class="deadline-description">
                    <h3>Description</h3>
                    <p>{{ $deadline->description ?: 'No description provided.' }}</p>
                </div>
                
                <div class="deadline-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['total_students'] ?? 0 }}</span>
                        <span class="stat-label">Total Students</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['completed'] ?? 0 }}</span>
                        <span class="stat-label">Completed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $stats['pending'] ?? 0 }}</span>
                        <span class="stat-label">Pending</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Actions Card -->
            <div class="actions-card">
                <h3><i class="fas fa-cog"></i> Actions</h3>
                <div class="action-buttons">
                    <a href="/lecturer/deadlines/{{ $deadline->id }}/edit" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit Deadline
                    </a>
                    <a href="/lecturer/deadlines" class="btn-view">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button onclick="confirmDelete({{ $deadline->id }})" class="btn-delete">
                        <i class="fas fa-trash"></i> Delete Deadline
                    </button>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Information</h3>
                <div class="info-item">
                    <span class="info-label">Created:</span>
                    <span class="info-value">{{ $deadline->created_at->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value">{{ $deadline->updated_at->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Days Remaining:</span>
                    <span class="info-value">{{ now()->diffInDays($deadline->due_date, false) }} days</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i></div>
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-message">Are you sure you want to delete this deadline? This action cannot be undone.</p>
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

<style>
.deadline-show-container {
    padding: 20px 0;
}

.row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.deadline-card, .actions-card, .info-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.deadline-header {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f5f9;
}

.deadline-title-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.deadline-title-section h1 {
    font-size: 1.8rem;
    color: #1e293b;
    margin: 0;
}

.deadline-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background: #fee2e2;
    color: #b91c1c;
}

.deadline-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.deadline-meta span {
    color: #64748b;
    font-size: 0.95rem;
}

.deadline-meta i {
    color: #f59e0b;
    margin-right: 8px;
}

.deadline-description h3 {
    color: #1e293b;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.deadline-description p {
    color: #475569;
    line-height: 1.7;
}

.deadline-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 2px solid #f1f5f9;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.stat-label {
    color: #64748b;
    font-size: 0.85rem;
}

.actions-card h3, .info-card h3 {
    color: #1e293b;
    font-size: 1.1rem;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.actions-card h3 i, .info-card h3 i {
    color: #f59e0b;
    margin-right: 8px;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn-edit, .btn-view, .btn-delete {
    padding: 14px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-edit {
    background: #14b8a6;
    color: white;
}

.btn-edit:hover {
    background: #0d9488;
    transform: translateY(-2px);
}

.btn-view {
    background: #667eea;
    color: white;
}

.btn-view:hover {
    background: #5a67d8;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    cursor: pointer;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #64748b;
    font-size: 0.9rem;
}

.info-value {
    color: #1e293b;
    font-weight: 600;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function confirmDelete(id) {
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/lecturer/deadlines/${id}`;
    modal.style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDelete');
    
    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
@endsection