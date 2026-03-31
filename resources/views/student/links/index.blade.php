@extends('student.layouts.master')

@section('title', 'Unit Links')
@section('page-icon', 'fa-link')
@section('page-title', 'Study Links - ' . $unit->code)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.units.available') }}">Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $unit->code }} Links</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .links-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .unit-header {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .unit-header h2 {
        color: #1e293b;
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .unit-header p {
        color: #64748b;
        margin-bottom: 15px;
    }

    .unit-header .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .unit-header .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }

    .links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .link-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .link-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        border-color: #f59e0b;
    }

    .link-card.favorite {
        border-left: 4px solid #f59e0b;
        background: #fffbeb;
    }

    .link-type {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .type-youtube {
        background: #fee2e2;
        color: #b91c1c;
    }

    .type-article {
        background: #dbeafe;
        color: #1e40af;
    }

    .type-other {
        background: #e2e8f0;
        color: #475569;
    }

    .link-title {
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .link-title a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.2s;
    }

    .link-title a:hover {
        color: #f59e0b;
    }

    .link-description {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .link-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
    }

    .link-actions {
        display: flex;
        gap: 10px;
    }

    .btn-favorite {
        background: none;
        border: none;
        color: #cbd5e1;
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-favorite:hover,
    .btn-favorite.active {
        color: #f59e0b;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #cbd5e1;
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-delete:hover {
        color: #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
        grid-column: 1 / -1;
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
</style>
@endpush

@section('content')
<div class="links-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="unit-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2><i class="fas fa-link" style="color: #f59e0b; margin-right: 10px;"></i> {{ $unit->code }} - {{ $unit->name }}</h2>
                <p>Save and organize useful study links for this unit</p>
            </div>
            <a href="{{ route('links.create', $unit->id) }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Add New Link
            </a>
        </div>
    </div>

    <div class="links-grid">
        @forelse($links as $link)
            <div class="link-card @if($link->is_favorite) favorite @endif" data-link-id="{{ $link->id }}">
                <div class="link-type type-{{ $link->type }}">
                    @if($link->type == 'youtube')
                        <i class="fab fa-youtube"></i> YouTube
                    @elseif($link->type == 'article')
                        <i class="fas fa-file-alt"></i> Article
                    @else
                        <i class="fas fa-link"></i> Other
                    @endif
                </div>
                
                <h3 class="link-title">
                    <a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a>
                </h3>
                
                @if($link->description)
                    <p class="link-description">{{ $link->description }}</p>
                @endif
                
                <div class="link-meta">
                    <span><i class="far fa-clock"></i> {{ $link->created_at->diffForHumans() }}</span>
                    
                    <div class="link-actions">
                        <a href="{{ route('links.edit', [$unit->id, $link->id]) }}" style="color: #94a3b8; margin-right: 8px;">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <button class="btn-favorite @if($link->is_favorite) active @endif" onclick="toggleFavorite('{{ $unit->id }}', {{ $link->id }}, this)">
                            <i class="fas fa-star"></i>
                        </button>
                        
                        <button class="btn-delete" onclick="confirmDelete('{{ $unit->id }}', {{ $link->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h3>No Links Yet</h3>
                <p>Start adding useful study links for this unit!</p>
                <a href="{{ route('links.create', $unit->id) }}" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Your First Link
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i></div>
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-message">Are you sure you want to delete this link? This action cannot be undone.</p>
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

<script>
    function toggleFavorite(unitId, linkId, button) {
        fetch(`/links/${unitId}/${linkId}/click`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Handle click tracking response
                console.log('Click tracked');
            }
        });
    }

    function confirmDelete(unitId, linkId) {
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/links/${unitId}/${linkId}`;
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