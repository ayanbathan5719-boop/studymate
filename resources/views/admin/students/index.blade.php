@extends('admin.layouts.master')

@section('title', 'Students Management')
@section('page-icon', 'fa-user-graduate')
@section('page-title', 'Students')

@push('styles')
<link rel="stylesheet" href="/css/admin/students.css">
<style>
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
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .modal-icon {
        font-size: 48px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .modal-title {
        font-size: 20px;
        font-weight: 600;
        color: #1e293b;
        text-align: center;
        margin-bottom: 10px;
    }
    
    .modal-message {
        color: #64748b;
        text-align: center;
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
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }
    
    .modal-btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .modal-btn-secondary:hover {
        background: #e2e8f0;
    }
    
    .modal-btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .modal-btn-danger:hover {
        background: #dc2626;
    }
    
    .student-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
    }
    
    .unit-badge {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        display: inline-block;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Students', 'url' => null],
    ]" />

    <!-- Search and Filter Bar -->
    <div class="search-container">
        <div class="search-box">
            <form method="GET" action="{{ route('admin.students.index') }}" style="display: flex; gap: 10px; width: 100%;">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search students by name or email..." 
                       value="{{ request('search') }}"
                       style="flex: 1;">
                
                <select name="unit_id" class="form-select" style="width: 200px;">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->code }})
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter" style="margin-right: 5px;"></i> Filter
                </button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 5px;"></i> Clear
                </a>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="success-message">
            <span style="font-size: 20px;"><i class="fas fa-check-circle"></i></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 20%;">Student</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 25%;">Enrolled Units</th>
                    <th style="width: 15%;">Joined</th>
                    <th style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td><span class="badge badge-secondary">#{{ $student->id }}</span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="student-avatar">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">{{ $student->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-secondary" style="background: #e2e8f0; color: #334155;">{{ $student->email }}</span>
                    </td>
                    <td>
                        @if($student->enrolledUnits->count() > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                @foreach($student->enrolledUnits->take(3) as $unit)
                                    <span class="badge badge-info unit-badge">
                                        {{ $unit->code }}
                                    </span>
                                @endforeach
                                @if($student->enrolledUnits->count() > 3)
                                    <span class="badge badge-secondary" style="background: #e2e8f0; color: #64748b;">+{{ $student->enrolledUnits->count() - 3 }}</span>
                                @endif
                            </div>
                        @else
                            <span class="badge badge-secondary" style="background: #e2e8f0; color: #94a3b8;">No units</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-secondary" style="font-size: 12px;">{{ $student->created_at->format('M d, Y') }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.students.show', $student) }}" class="btn-sm" style="background: #14b8a6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <button type="button" 
                                    class="btn-sm delete-btn" 
                                    data-id="{{ $student->id }}" 
                                    data-name="{{ $student->name }}"
                                    style="background: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-user-graduate"></i></div>
                        <h3>No Students Found</h3>
                        <p>Students will appear here once they register.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    @if($students->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 0 10px;">
            <div style="color: #64748b; font-size: 14px;">
                Showing <span style="font-weight: 600;">{{ $students->firstItem() }}</span> 
                to <span style="font-weight: 600;">{{ $students->lastItem() }}</span> 
                of <span style="font-weight: 600;">{{ $students->total() }}</span> students
            </div>
            
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                {{ $students->links() }}
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon"><i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 48px;"></i></div>
            <h3 class="modal-title">Confirm Deletion</h3>
            <p class="modal-message" id="deleteMessage">Are you sure you want to delete this student? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="modal-btn modal-btn-secondary">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="modal-btn modal-btn-danger">
                        <i class="fas fa-trash" style="margin-right: 5px;"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete modal functionality
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteMessage = document.getElementById('deleteMessage');
        const cancelDelete = document.getElementById('cancelDelete');
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.dataset.id;
                const studentName = this.dataset.name;
                deleteForm.action = `/admin/students/${studentId}`;
                deleteMessage.textContent = `Are you sure you want to delete "${studentName}"? This action cannot be undone.`;
                modal.style.display = 'flex';
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>
@endpush