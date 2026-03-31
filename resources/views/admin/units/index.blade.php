@extends('admin.layouts.master')

@section('title', 'Units Management')
@section('page-icon', 'fa-layer-group')
@section('page-title', 'Units')

@push('styles')
<link rel="stylesheet" href="/css/admin/units.css?v={{ time() }}">
@endpush

@section('content')
<!-- Breadcrumb -->
<x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Units', 'url' => null],
    ]" />

<!-- Search Bar -->
<div class="search-container">
    <div class="search-box">
        <input type="text"
            id="searchInput"
            class="search-input"
            placeholder="Search units by name, code, course, or lecturer...">
        <span class="search-icon"><i class="fas fa-search"></i></span>
    </div>
    <a href="/admin/units/create" class="add-btn">
        <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> Add New Unit
    </a>
</div>

@if(session('success'))
<div style="background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
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
                <th style="width: 20%;">Unit Name</th>
                <th style="width: 10%;">Code</th>
                <th style="width: 20%;">Course</th>
                <th style="width: 20%;">Lecturer</th>
                <th style="width: 10%;">Created</th>
                <th style="width: 15%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $unit)
            <tr>
                <td><span class="badge badge-secondary">#{{ $unit->id }}</span></td>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $unit->name }}</div>
                </td>
                <td>
                    <span class="badge badge-info">{{ $unit->code }}</span>
                </td>
                <td>
                    @if($unit->course)
                    <span class="badge badge-secondary">{{ $unit->course->name }}</span>
                    @else
                    <span class="badge badge-secondary" style="background: #e2e8f0; color: #64748b;">No course</span>
                    @endif
                </td>
                <td>
                    @if($unit->lecturer)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="user-avatar-small" style="width: 28px; height: 28px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">
                            {{ substr($unit->lecturer->name, 0, 1) }}
                        </div>
                        <span>{{ $unit->lecturer->name }}</span>
                    </div>
                    @else
                    <span class="badge badge-secondary" style="background: #e2e8f0; color: #64748b;">Not assigned</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-secondary" style="font-size: 12px;">{{ $unit->created_at->format('M d, Y') }}</span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="/admin/units/{{ $unit->id }}/edit" class="btn-sm" style="background: #14b8a6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button"
                                class="btn-sm delete-btn"
                                data-id="{{ $unit->id }}"
                                data-name="{{ $unit->name }}"
                                style="background: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-layer-group"></i></div>
                    <h3>No Units Found</h3>
                    <p>Get started by creating your first unit.</p>
                    <a href="/admin/units/create" class="add-btn" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus-circle"></i> Create New Unit
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($units->hasPages())
<div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 0 10px;">
    <div style="color: #64748b; font-size: 14px;">
        Showing <span style="font-weight: 600;">{{ $units->firstItem() }}</span>
        to <span style="font-weight: 600;">{{ $units->lastItem() }}</span>
        of <span style="font-weight: 600;">{{ $units->total() }}</span> units
    </div>

    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
        {{-- Previous Page Link --}}
        @if($units->onFirstPage())
        <span style="padding: 8px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8; cursor: not-allowed; font-size: 14px;">← Previous</span>
        @else
        <a href="{{ $units->previousPageUrl() }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">← Previous</a>
        @endif

        {{-- Page Numbers --}}
        @php
        $start = max(1, $units->currentPage() - 2);
        $end = min($units->lastPage(), $units->currentPage() + 2);
        @endphp

        @if($start > 1)
        <a href="{{ $units->url(1) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">1</a>
        @if($start > 2)
        <span style="padding: 8px 14px; color: #94a3b8;">...</span>
        @endif
        @endif

        @for($page = $start; $page <= $end; $page++)
            @if($page==$units->currentPage())
            <span style="padding: 8px 14px; background: #667eea; border: 1px solid #667eea; border-radius: 8px; color: white; font-size: 14px; font-weight: 600;">{{ $page }}</span>
            @else
            <a href="{{ $units->url($page) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">{{ $page }}</a>
            @endif
            @endfor

            @if($end < $units->lastPage())
                @if($end < $units->lastPage() - 1)
                    <span style="padding: 8px 14px; color: #94a3b8;">...</span>
                    @endif
                    <a href="{{ $units->url($units->lastPage()) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">{{ $units->lastPage() }}</a>
                    @endif

                    {{-- Next Page Link --}}
                    @if($units->hasMorePages())
                    <a href="{{ $units->nextPageUrl() }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">Next →</a>
                    @else
                    <span style="padding: 8px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8; cursor: not-allowed; font-size: 14px;">Next →</span>
                    @endif
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 48px;"></i></div>
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-message" id="deleteMessage">Are you sure you want to delete this unit? This action cannot be undone.</p>
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
        const searchInput = document.getElementById('searchInput');
        const table = document.querySelector('table tbody');
        const rows = table.querySelectorAll('tr');
        const modal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteMessage = document.getElementById('deleteMessage');
        const cancelDelete = document.getElementById('cancelDelete');

        // Don't show search if there are no rows or only empty state row
        if (rows.length === 1 && rows[0].querySelector('td[colspan]')) {
            searchInput.disabled = true;
            searchInput.placeholder = 'No units to search';
            return;
        }

        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();

            rows.forEach(row => {
                // Skip the empty state row
                if (row.querySelector('td[colspan]')) return;

                const name = row.cells[1]?.querySelector('div')?.textContent.toLowerCase() || '';
                const code = row.cells[2]?.querySelector('span')?.textContent.toLowerCase() || '';
                const course = row.cells[3]?.textContent.toLowerCase() || '';
                const lecturer = row.cells[4]?.textContent.toLowerCase() || '';

                if (name.includes(searchTerm) ||
                    code.includes(searchTerm) ||
                    course.includes(searchTerm) ||
                    lecturer.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "no results" message if all rows hidden
            let visibleCount = 0;
            rows.forEach(row => {
                if (row.style.display !== 'none' && !row.querySelector('td[colspan]')) {
                    visibleCount++;
                }
            });

            // Remove existing no-results row if any
            const existingNoResults = document.getElementById('no-search-results');
            if (existingNoResults) existingNoResults.remove();

            if (visibleCount === 0 && searchTerm !== '') {
                const noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-search-results';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                        <h3>No Matching Units</h3>
                        <p>No units found matching "${searchTerm}"</p>
                    </td>
                `;
                table.appendChild(noResultsRow);
            }
        });

        // Clear search on Escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('keyup'));
            }
        });

        // Delete modal functionality
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const unitId = this.dataset.id;
                const unitName = this.dataset.name;
                deleteForm.action = `/admin/units/${unitId}`;
                deleteMessage.textContent = `Are you sure you want to delete "${unitName}"? This action cannot be undone.`;
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