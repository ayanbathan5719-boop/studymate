@extends('admin.layouts.master')

@section('title', 'Unit Assignments')
@section('page-icon', 'fa-tasks')
@section('page-title', 'Unit Assignments')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/units">Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assignments</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .assignments-container {
        padding: 20px 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 24px;
    }

    .stat-content h3 {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    /* Filters */
    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    .filters-form {
        display: flex;
        gap: 20px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .filter-group label i {
        color: #f59e0b;
        margin-right: 5px;
    }

    .filter-select, .filter-input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .btn-filter {
        padding: 10px 24px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-filter:hover {
        background: #5a67d8;
        transform: translateY(-2px);
    }

    .btn-clear {
        padding: 10px 24px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Two Panel Layout */
    .assignment-panels {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-top: 20px;
    }

    .panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .panel-header {
        background: #f8fafc;
        padding: 20px;
        border-bottom: 2px solid #e2e8f0;
    }

    .panel-header h2 {
        font-size: 1.2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .panel-header h2 i {
        color: #f59e0b;
    }

    .panel-header .badge {
        background: #f59e0b;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-left: 10px;
    }

    .panel-content {
        max-height: 500px;
        overflow-y: auto;
        padding: 10px;
    }

    /* Unit Items */
    .unit-item {
        position: relative;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .unit-item:hover {
        border-color: #f59e0b;
        transform: translateX(4px);
        background: #fffbeb;
    }

    .unit-item.selected {
        border-color: #f59e0b;
        background: #fffbeb;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .unit-item.assigned {
        background: #f1f5f9;
        border-color: #94a3b8;
        opacity: 0.8;
    }

    .quick-unassign {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #ef4444;
        cursor: pointer;
        font-size: 18px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .unit-item:hover .quick-unassign {
        opacity: 1;
    }

    .quick-unassign:hover {
        transform: scale(1.2);
    }

    .unit-code {
        font-size: 0.8rem;
        font-weight: 600;
        color: #f59e0b;
        background: #fffbeb;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .unit-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .unit-course {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .unit-course i {
        color: #f59e0b;
    }

    .current-lecturer-badge {
        position: absolute;
        top: 15px;
        right: 40px;
        background: #e2e8f0;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .current-lecturer-badge i {
        color: #10b981;
    }

    /* Lecturer Items */
    .lecturer-item {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .lecturer-item:hover {
        border-color: #f59e0b;
        transform: translateX(4px);
        background: #fffbeb;
    }

    .lecturer-item.selected {
        border-color: #f59e0b;
        background: #fffbeb;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
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
    }

    .lecturer-info {
        flex: 1;
    }

    .lecturer-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .lecturer-email {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .lecturer-email i {
        color: #f59e0b;
    }

    .lecturer-department {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Action Buttons */
    .action-bar {
        margin-top: 25px;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding: 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .btn-assign {
        padding: 12px 30px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-assign:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .btn-assign:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-unassign {
        padding: 12px 30px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-unassign:hover:not(:disabled) {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .btn-unassign:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .empty-message i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .assignment-panels {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="assignments-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="stat-content">
                <h3>Total Units</h3>
                <div class="stat-value">{{ $totalUnits }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle" style="color: #10b981;"></i></div>
            <div class="stat-content">
                <h3>Assigned</h3>
                <div class="stat-value">{{ $assignedUnits }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-exclamation-circle" style="color: #ef4444;"></i></div>
            <div class="stat-content">
                <h3>Unassigned</h3>
                <div class="stat-value">{{ $unassignedUnits }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
            <div class="stat-content">
                <h3>Lecturers</h3>
                <div class="stat-value">{{ $totalLecturers }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" action="{{ route('admin.units.assignments') }}" class="filters-form">
            <div class="filter-group">
                <label><i class="fas fa-book"></i> Course</label>
                <select name="course_id" class="filter-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Status</label>
                <select name="status" class="filter-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Units</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned Only</option>
                    <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
            <a href="{{ route('admin.units.assignments') }}" class="btn-clear">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
    </div>

    <!-- Two Panel Assignment Interface -->
    <div class="assignment-panels">
        <!-- Left Panel: Units -->
        <div class="panel">
            <div class="panel-header">
                <h2>
                    <i class="fas fa-layer-group"></i> Units
                    <span class="badge">{{ $units->count() }}</span>
                </h2>
            </div>
            <div class="panel-content" id="unitsPanel">
                @forelse($units as $unit)
                    <div class="unit-item {{ $unit->lecturer ? 'assigned' : '' }}" 
                         data-unit-id="{{ $unit->id }}"
                         data-unit-code="{{ $unit->code }}"
                         data-unit-name="{{ $unit->name }}"
                         data-course-name="{{ $unit->course->name ?? 'No Course' }}"
                         data-lecturer-id="{{ $unit->lecturer_id ?? '' }}">
                        @if($unit->lecturer)
                            <div class="current-lecturer-badge">
                                <i class="fas fa-user-check"></i> {{ $unit->lecturer->name }}
                            </div>
                        @endif
                        <span class="unit-code">{{ $unit->code }}</span>
                        <div class="unit-name">{{ $unit->name }}</div>
                        <div class="unit-course">
                            <i class="fas fa-book"></i> {{ $unit->course->name ?? 'No Course' }}
                        </div>
                    </div>
                @empty
                    <div class="empty-message">
                        <i class="fas fa-layer-group"></i>
                        <p>No units found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Panel: Lecturers -->
        <div class="panel">
            <div class="panel-header">
                <h2>
                    <i class="fas fa-chalkboard-user"></i> Lecturers
                    <span class="badge">{{ $lecturers->count() }}</span>
                </h2>
            </div>
            <div class="panel-content" id="lecturersPanel">
                @forelse($lecturers as $lecturer)
                    <div class="lecturer-item" data-lecturer-id="{{ $lecturer->id }}">
                        <div class="lecturer-avatar">{{ substr($lecturer->name, 0, 1) }}</div>
                        <div class="lecturer-info">
                            <div class="lecturer-name">{{ $lecturer->name }}</div>
                            <div class="lecturer-email">
                                <i class="fas fa-envelope"></i> {{ $lecturer->email }}
                            </div>
                            <div class="lecturer-department">{{ $lecturer->department ?? 'No department' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-message">
                        <i class="fas fa-chalkboard-user"></i>
                        <p>No lecturers available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <form id="assignForm" method="POST" action="" style="display: inline;">
            @csrf
            @method('PUT')
            <input type="hidden" name="lecturer_id" id="selectedLecturerId">
            <button type="submit" class="btn-assign" id="assignBtn" disabled>
                <i class="fas fa-check-circle"></i> Assign Selected Lecturer
            </button>
        </form>
        
        <form id="unassignForm" method="POST" action="" style="display: inline;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn-unassign" id="unassignBtn" disabled>
                <i class="fas fa-times-circle"></i> Remove Lecturer
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedUnit = null;
        let selectedLecturer = null;
        
        const allUnits = document.querySelectorAll('.unit-item');
        const lecturers = document.querySelectorAll('.lecturer-item');
        const assignBtn = document.getElementById('assignBtn');
        const unassignBtn = document.getElementById('unassignBtn');
        const assignForm = document.getElementById('assignForm');
        const unassignForm = document.getElementById('unassignForm');
        const selectedLecturerInput = document.getElementById('selectedLecturerId');
        
        // ONE-CLICK UNASSIGN - Add quick unassign icons to assigned units
        document.querySelectorAll('.unit-item.assigned').forEach(unit => {
            const unassignQuick = document.createElement('span');
            unassignQuick.className = 'quick-unassign';
            unassignQuick.innerHTML = '<i class="fas fa-times-circle"></i>';
            unassignQuick.title = 'Click to unassign';
            
            unassignQuick.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent unit selection
                if (confirm('Remove lecturer from this unit?')) {
                    const unitId = unit.dataset.unitId;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/units/${unitId}/unassign`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';
                    
                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            
            unit.appendChild(unassignQuick);
        });
        
        // Unit selection
        allUnits.forEach(unit => {
            unit.addEventListener('click', function() {
                // Remove selection from all units
                allUnits.forEach(u => u.classList.remove('selected'));
                
                // Select this unit
                this.classList.add('selected');
                selectedUnit = this;
                
                // Update forms
                assignForm.action = `/admin/units/${this.dataset.unitId}/assign`;
                unassignForm.action = `/admin/units/${this.dataset.unitId}/unassign`;
                
                // Enable/disable buttons based on selection
                if (this.classList.contains('assigned')) {
                    // Unit has lecturer - enable unassign only
                    unassignBtn.disabled = false;
                    assignBtn.disabled = true;
                } else {
                    // Unit has no lecturer - enable assign if lecturer selected
                    unassignBtn.disabled = true;
                    if (selectedLecturer) {
                        assignBtn.disabled = false;
                    }
                }
            });

            // Double-click to unassign
            if (unit.classList.contains('assigned')) {
                unit.addEventListener('dblclick', function() {
                    if (confirm('Remove lecturer from this unit?')) {
                        const unitId = this.dataset.unitId;
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/units/${unitId}/unassign`;
                        
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        
                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'PUT';
                        
                        form.appendChild(csrf);
                        form.appendChild(method);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
        
        // Lecturer selection
        lecturers.forEach(lecturer => {
            lecturer.addEventListener('click', function() {
                // Remove selection from all lecturers
                lecturers.forEach(l => l.classList.remove('selected'));
                
                // Select this lecturer
                this.classList.add('selected');
                selectedLecturer = this;
                selectedLecturerInput.value = this.dataset.lecturerId;
                
                // Enable assign button if a unit is selected AND it's not assigned
                if (selectedUnit && !selectedUnit.classList.contains('assigned')) {
                    assignBtn.disabled = false;
                }
            });
        });
        
        // Assign button validation
        assignBtn.addEventListener('click', function(e) {
            if (!selectedUnit || !selectedLecturer) {
                e.preventDefault();
                alert('Please select both a unit and a lecturer');
            } else if (selectedUnit.classList.contains('assigned')) {
                e.preventDefault();
                alert('This unit already has a lecturer. Please unassign first.');
            }
        });
        
        // Unassign button validation
        unassignBtn.addEventListener('click', function(e) {
            if (!selectedUnit) {
                e.preventDefault();
                alert('Please select a unit to unassign');
            }
        });
    });
</script>
@endsection