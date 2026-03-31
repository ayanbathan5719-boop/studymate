@extends('admin.layouts.master')

@section('title', 'Assign Units')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'Assign Units to ' . $lecturer->name)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/lecturers">Lecturers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Units</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="assign-units-container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-info-circle"></i> Lecturer Information</h4>
                </div>
                <div class="card-body">
                    <div class="lecturer-info">
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $lecturer->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $lecturer->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Department:</span>
                            <span class="info-value">{{ $lecturer->department }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Currently Teaching:</span>
                            <span class="info-value">{{ $assignedUnits->count() }} units</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4><i class="fas fa-layer-group"></i> Available Units</h4>
                    <p class="text-muted">Select units to assign to this lecturer</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/lecturers/{{ $lecturer->id }}/units">
                        @csrf
                        
                        <div class="units-grid">
                            @foreach($allUnits as $unit)
                                <div class="unit-checkbox-item">
                                    <label class="checkbox-label">
                                        <input type="checkbox" 
                                               name="units[]" 
                                               value="{{ $unit->id }}"
                                               {{ in_array($unit->id, $assignedUnitIds) ? 'checked' : '' }}>
                                        <span class="unit-code">{{ $unit->code }}</span>
                                        <span class="unit-name">{{ $unit->name }}</span>
                                        <span class="unit-course">({{ $unit->course->name ?? 'No Course' }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Assignments
                            </button>
                            <a href="/admin/lecturers" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.assign-units-container {
    padding: 20px 0;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-header {
    background: #f8fafc;
    padding: 20px 25px;
    border-bottom: 1px solid #e2e8f0;
}

.card-header h4 {
    margin: 0;
    color: #1e293b;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h4 i {
    color: #f59e0b;
}

.card-header p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
}

.card-body {
    padding: 25px;
}

.lecturer-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    font-size: 0.8rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
}

.units-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
}

.unit-checkbox-item {
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.unit-checkbox-item:hover {
    border-color: #f59e0b;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    cursor: pointer;
    width: 100%;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #f59e0b;
}

.unit-code {
    font-weight: 600;
    color: #f59e0b;
    min-width: 80px;
}

.unit-name {
    color: #1e293b;
    flex: 1;
}

.unit-course {
    color: #64748b;
    font-size: 0.85rem;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

@media (max-width: 768px) {
    .lecturer-info {
        grid-template-columns: 1fr;
    }
    
    .units-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection