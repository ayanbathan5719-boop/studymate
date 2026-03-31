@extends('lecturer.layouts.master')

@section('title', 'Manage Topics')
@section('page-icon', 'fa-list-ul')
@section('page-title', 'Manage Topics - ' . $unit->name)

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/lecturer/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/lecturer/units">My Units</a></li>
            <li class="breadcrumb-item active" aria-current="page">Topics</li>
        </ol>
    </nav>
@endsection

@push('styles')
<style>
    .topics-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Unit Header */
    .unit-header {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .unit-info h2 {
        color: #1e293b;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .unit-code {
        color: #f59e0b;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .btn-add {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 12px 25px;
        border-radius: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        color: white;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: #fef3c7;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 1.5rem;
    }

    .stat-info h4 {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-info .stat-value {
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 600;
    }

    /* Topics List */
    .topics-list {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .list-header {
        background: #f8fafc;
        padding: 15px 20px;
        border-bottom: 2px solid #e2e8f0;
        display: grid;
        grid-template-columns: 50px 1fr 120px 120px 100px;
        font-weight: 600;
        color: #475569;
    }

    .topic-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: grid;
        grid-template-columns: 50px 1fr 120px 120px 100px;
        align-items: center;
        transition: all 0.2s ease;
        cursor: move;
    }

    .topic-item:hover {
        background: #f8fafc;
    }

    .topic-item.dragging {
        opacity: 0.5;
        background: #e2e8f0;
    }

    .drag-handle {
        color: #94a3b8;
        cursor: move;
        font-size: 1.2rem;
    }

    .topic-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .topic-title h3 {
        color: #1e293b;
        font-size: 1rem;
        margin: 0;
    }

    .topic-description {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 3px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-draft {
        background: #f1f5f9;
        color: #64748b;
    }

    .badge-published {
        background: #d1fae5;
        color: #065f46;
    }

    .topic-time {
        color: #64748b;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .topic-actions {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-edit {
        background: #fef3c7;
        color: #f59e0b;
    }

    .btn-edit:hover {
        background: #f59e0b;
        color: white;
    }

    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }

    .btn-toggle {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-toggle:hover {
        background: #cbd5e1;
    }

    .btn-toggle.published {
        background: #d1fae5;
        color: #10b981;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #1e293b;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 25px;
    }

    /* Save Order Button */
    .save-order-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 25px rgba(245, 158, 11, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .save-order-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.5);
    }

    .save-order-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="topics-container">
    <!-- Unit Header -->
    <div class="unit-header">
        <div class="unit-info">
            <h2>{{ $unit->name }}</h2>
            <span class="unit-code"><i class="fas fa-code"></i> {{ $unit->code }}</span>
        </div>
        <a href="{{ route('lecturer.topics.create', $unit->code) }}" class="btn-add">
            <i class="fas fa-plus-circle"></i> Add New Topic
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-list-ul"></i>
            </div>
            <div class="stat-info">
                <h4>Total Topics</h4>
                <div class="stat-value">{{ $topics->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Published</h4>
                <div class="stat-value">{{ $topics->where('status', 'published')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Total Time</h4>
                <div class="stat-value">{{ $topics->sum('estimated_minutes') }}m</div>
            </div>
        </div>
    </div>

    <!-- Topics List -->
    <div class="topics-list" id="topicsList">
        @if($topics->count() > 0)
            <div class="list-header">
                <div></div>
                <div>Topic</div>
                <div>Status</div>
                <div>Time</div>
                <div>Actions</div>
            </div>

            <div id="sortableTopics">
                @foreach($topics as $topic)
                    <div class="topic-item" data-id="{{ $topic->id }}" data-order="{{ $topic->order }}">
                        <div class="drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="topic-title">
                            <div>
                                <h3>{{ $topic->title }}</h3>
                                @if($topic->description)
                                    <p class="topic-description">{{ Str::limit($topic->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="badge {{ $topic->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                                {{ ucfirst($topic->status) }}
                            </span>
                        </div>
                        <div class="topic-time">
                            <i class="far fa-clock"></i>
                            {{ $topic->formatted_time }}
                        </div>
                        <div class="topic-actions">
                            <button onclick="toggleStatus({{ $topic->id }})" 
                                    class="btn-action btn-toggle {{ $topic->status === 'published' ? 'published' : '' }}">
                                <i class="fas {{ $topic->status === 'published' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                            </button>
                            <a href="{{ route('lecturer.topics.edit', [$unit->code, $topic->id]) }}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteTopic({{ $topic->id }})" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-list-ul"></i>
                </div>
                <h3>No Topics Yet</h3>
                <p>Start organizing your unit content by creating topics.</p>
                <a href="{{ route('lecturer.topics.create', $unit->code) }}" class="btn-add">
                    <i class="fas fa-plus-circle"></i> Create Your First Topic
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Save Order Button -->
@if($topics->count() > 1)
    <button id="saveOrderBtn" class="save-order-btn" style="display: none;">
        <i class="fas fa-save"></i> Save New Order
    </button>
@endif

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    let sortable;
    let orderChanged = false;

    @if($topics->count() > 1)
        // Initialize Sortable
        const topicsContainer = document.getElementById('sortableTopics');
        sortable = new Sortable(topicsContainer, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'dragging',
            onEnd: function() {
                orderChanged = true;
                document.getElementById('saveOrderBtn').style.display = 'flex';
            }
        });
    @endif

    // Save new order
    document.getElementById('saveOrderBtn')?.addEventListener('click', function() {
        const topics = [];
        document.querySelectorAll('.topic-item').forEach((item, index) => {
            topics.push({
                id: item.dataset.id,
                order: index + 1
            });
        });

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch('{{ route("lecturer.topics.reorder", $unit->code) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ topics: topics })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orderChanged = false;
                btn.style.display = 'none';
                showNotification('Topic order updated successfully!', 'success');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save New Order';
        });
    });

    // Toggle status
    function toggleStatus(id) {
        fetch(`/lecturer/topics/{{ $unit->code }}/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Delete topic
    function deleteTopic(id) {
        if (confirm('Are you sure you want to delete this topic? This action cannot be undone.')) {
            const form = document.getElementById('deleteForm');
            form.action = `/lecturer/topics/{{ $unit->code }}/${id}`;
            form.submit();
        }
    }

    // Show notification
    function showNotification(message, type = 'success') {
        // You can implement a toast notification here
        alert(message);
    }

    // Warn before leaving if order changed
    window.addEventListener('beforeunload', function(e) {
        if (orderChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes to the topic order.';
        }
    });
</script>
@endpush
@endsection