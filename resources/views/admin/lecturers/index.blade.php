@extends('admin.layouts.master')

@section('title', 'Lecturers Management')
@section('page-icon', 'fa-chalkboard-user')
@section('page-title', 'Lecturers')

@push('styles')
<link rel="stylesheet" href="/css/admin/lecturers.css">
<style>
    .table-container {
        margin-top: 20px;
    }
    
    .search-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .search-box {
        flex: 1;
        max-width: 400px;
        position: relative;
    }
    
    .search-input {
        width: 100%;
        padding: 12px 20px 12px 45px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    
    .search-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    
    .add-btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }
    
    .success-message {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th {
        background: #f8fafc;
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table td {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .lecturer-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 18px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover .lecturer-avatar {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .badge-secondary {
        background: #e2e8f0;
        color: #475569;
    }
    
    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .table-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background: #14b8a6;
        color: white;
    }
    
    .btn-edit:hover {
        background: #0d9488;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
    }
    
    .btn-delete:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    
    .empty-state h3 {
        color: #334155;
        font-size: 1.3rem;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        margin-bottom: 20px;
    }
    
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .pagination-info {
        color: #64748b;
        font-size: 0.9rem;
    }
    
    .pagination-links {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .pagination-link {
        padding: 8px 14px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .pagination-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    .pagination-link.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }
    
    .pagination-link.disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    }
    
    .modal-icon {
        font-size: 48px;
        color: #f59e0b;
        margin-bottom: 15px;
    }
    
    .modal-title {
        font-size: 1.3rem;
        color: #1e293b;
        margin-bottom: 10px;
    }
    
    .modal-message {
        color: #64748b;
        margin-bottom: 25px;
    }
    
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .modal-btn {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .modal-btn-secondary {
        background: #e2e8f0;
        color: #475569;
    }
    
    .modal-btn-secondary:hover {
        background: #cbd5e1;
    }
    
    .modal-btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .modal-btn-danger:hover {
        background: #dc2626;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Lecturers', 'url' => null],
    ]" />

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-box">
            <span class="search-icon"><i class="fas fa-search"></i></span>
            <input type="text" 
                   id="searchInput" 
                   class="search-input" 
                   placeholder="Search lecturers by name, email, or department...">
        </div>
        <a href="/admin/lecturers/create" class="add-btn">
            <i class="fas fa-plus-circle"></i> Add New Lecturer
        </a>
    </div>

    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lecturers as $lecturer)
                <tr>
                    <td>
                        <div class="lecturer-avatar">
                            {{ substr($lecturer->name, 0, 1) }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $lecturer->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $lecturer->email }}</span>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $lecturer->department }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <!-- Edit Button -->
                            <a href="/admin/lecturers/{{ $lecturer->id }}/edit" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <!-- Delete Button -->
                            <button type="button" 
                                    class="btn-action btn-delete delete-btn" 
                                    data-id="{{ $lecturer->id }}" 
                                    data-name="{{ $lecturer->name }}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        <i class="fas fa-chalkboard-user"></i>
                        <h3>No Lecturers Found</h3>
                        <p>Get started by adding your first lecturer.</p>
                        <a href="/admin/lecturers/create" class="add-btn" style="display: inline-flex;">
                            <i class="fas fa-plus-circle"></i> Add New Lecturer
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    @if($lecturers->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <span style="font-weight: 600;">{{ $lecturers->firstItem() }}</span> 
                to <span style="font-weight: 600;">{{ $lecturers->lastItem() }}</span> 
                of <span style="font-weight: 600;">{{ $lecturers->total() }}</span> lecturers
            </div>
            
            <div class="pagination-links">
                {{-- Previous Page Link --}}
                @if($lecturers->onFirstPage())
                    <span class="pagination-link disabled">←</span>
                @else
                    <a href="{{ $lecturers->previousPageUrl() }}" class="pagination-link">←</a>
                @endif

                {{-- Page Numbers --}}
                @foreach($lecturers->getUrlRange(max(1, $lecturers->currentPage() - 2), min($lecturers->lastPage(), $lecturers->currentPage() + 2)) as $page => $url)
                    @if($page == $lecturers->currentPage())
                        <span class="pagination-link active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($lecturers->hasMorePages())
                    <a href="{{ $lecturers->nextPageUrl() }}" class="pagination-link">→</a>
                @else
                    <span class="pagination-link disabled">→</span>
                @endif
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="modal-title">Delete Lecturer</h3>
            <p class="modal-message" id="deleteMessage">Are you sure you want to delete this lecturer?</p>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.querySelector('table tbody');
        const rows = table.querySelectorAll('tr');
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteMessage = document.getElementById('deleteMessage');
        const cancelDelete = document.getElementById('cancelDelete');
        
        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) return;
                    
                    const name = row.cells[1]?.textContent.toLowerCase() || '';
                    const email = row.cells[2]?.textContent.toLowerCase() || '';
                    const department = row.cells[3]?.textContent.toLowerCase() || '';
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm) || department.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Clear on Escape
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('keyup'));
                }
            });
        }
        
        // Delete modal
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const lecturerId = this.dataset.id;
                const lecturerName = this.dataset.name;
                deleteForm.action = `/admin/lecturers/${lecturerId}`;
                deleteMessage.textContent = `Are you sure you want to delete "${lecturerName}"? This action cannot be undone.`;
                modal.style.display = 'flex';
            });
        });
        
        if (cancelDelete) {
            cancelDelete.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>
@endpush