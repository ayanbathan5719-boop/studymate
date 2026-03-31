@extends('student.layouts.master')

@section('title', 'My Custom Units')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'My Custom Units')

@push('styles')
<style>
    .custom-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .custom-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .custom-header h1 {
        font-size: 2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .custom-header h1 i {
        color: #f59e0b;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .btn-outline {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-outline:hover {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    .btn-edit {
        background: #14b8a6;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        background: #0d9488;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .success-message {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .custom-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .custom-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .custom-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .card-header {
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .unit-icon {
        width: 50px;
        height: 50px;
        background: {{ $customUnit->color ?? '#f59e0b' }};
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .unit-info h3 {
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .unit-info p {
        color: #64748b;
        font-size: 0.85rem;
    }

    .card-body {
        padding: 20px;
    }

    .progress-section {
        margin-bottom: 20px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9rem;
        color: #475569;
    }

    .progress-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .goal-info {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 8px;
    }

    .card-footer {
        padding: 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #334155;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 25px;
    }

    @media (max-width: 768px) {
        .custom-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="custom-container">
    <div class="custom-header">
        <h1><i class="fas fa-layer-group"></i> My Custom Units</h1>
        <a href="{{ route('student.custom.create') }}" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Create Custom Unit
        </a>
    </div>

    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="custom-grid">
        @forelse($customUnits as $unit)
            <div class="custom-card">
                <div class="card-header">
                    <div class="unit-icon" style="background: {{ $unit->color }}">
                        <i class="fas {{ $unit->icon }}"></i>
                    </div>
                    <div class="unit-info">
                        <h3>{{ $unit->name }}</h3>
                        <p>Created {{ $unit->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="card-body">
                    @if($unit->description)
                        <p style="color: #64748b; margin-bottom: 20px;">{{ $unit->description }}</p>
                    @endif

                    @if($unit->goal_minutes)
                        <div class="progress-section">
                            <div class="progress-header">
                                <span>Progress</span>
                                <span>{{ $unit->progress }} / {{ $unit->goal_minutes }} min</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $unit->progress_percentage }}%"></div>
                            </div>
                            <div class="goal-info">
                                <i class="fas fa-bullseye"></i> Goal: {{ $unit->goal_minutes }} minutes
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('student.custom.edit', $unit) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('student.custom.destroy', $unit) }}" style="display: inline;" onsubmit="return confirm('Delete this custom unit?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <h3>No Custom Units Yet</h3>
                <p>Create your own personal study units to track your learning goals!</p>
                <a href="{{ route('student.custom.create') }}" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Create Your First Unit
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection