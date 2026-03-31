@extends('admin.layouts.master')

@section('title', 'All Resources')
@section('page-icon', 'fa-folder-open')
@section('page-title', 'All Resources')

@section('content')
<div style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 1.5rem; font-weight: 600; color: #1e293b;">
            <i class="fas fa-folder-open"></i> All Resources
        </h1>
        <p style="color: #64748b; margin-top: 4px;">View and manage all resources in the system</p>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
        @forelse($resources as $resource)
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; display: flex; gap: 16px; transition: all 0.2s;">
                <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; background: #fef3c7; color: #d97706;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 8px;">{{ Str::limit($resource->title, 50) }}</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.7rem; color: #64748b; margin-bottom: 8px;">
                        <span><i class="fas fa-layer-group"></i> Unit: {{ $resource->unit_code ?? 'N/A' }}</span>
                        <span><i class="fas fa-user"></i> By: {{ $resource->user->name ?? 'Unknown' }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $resource->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($resource->description)
                        <div style="font-size: 0.8rem; color: #475569;">{{ Str::limit($resource->description, 80) }}</div>
                    @endif
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('admin.resources.show', $resource) }}" style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <button onclick="confirmDelete({{ $resource->id }})" style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; border: 2px dashed #e2e8f0;">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #334155;">No Resources Found</h3>
                <p style="color: #64748b;">No resources have been uploaded yet.</p>
            </div>
        @endforelse
    </div>

    @if($resources->hasPages())
        <div style="margin-top: 32px; display: flex; justify-content: center;">
            {{ $resources->links() }}
        </div>
    @endif
</div>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to delete this resource? This action cannot be undone.')) {
        fetch(`/admin/resources/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if(response.ok) {
                location.reload();
            } else {
                alert('Error deleting resource');
            }
        });
    }
}
</script>
@endsection