@extends('admin.layouts.master')

@section('title', 'Courses Management')
@section('page-icon', 'fa-book')
@section('page-title', 'Courses')

@push('styles')
<link rel="stylesheet" href="/css/admin/courses.css?v={{ time() }}">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Courses', 'url' => null],
    ]" />

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-box">
            <input type="text"
                   id="searchInput"
                   class="search-input"
                   placeholder="Search courses by name or code...">
            <span class="search-icon"><i class="fas fa-search"></i></span>
        </div>
        <a href="/admin/courses/create" class="add-btn">
            <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> Add New Course
        </a>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 20%;">Course Name</th>
                    <th style="width: 10%;">Code</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 15%;">Created By</th>
                    <th style="width: 10%;">Created</th>
                    <th style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr>
                    <td><span class="badge badge-secondary">#{{ $course->id }}</span></td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $course->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $course->code }}</span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <div style="color: #475569; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $course->description ?? 'No description' }}
                            </div>
                            @if($course->description && strlen($course->description) > 50)
                                <span class="description-link" onclick='showDescription({{ json_encode($course->description) }})'>View More</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="creator-avatar">
                                {{ substr($course->creator->name ?? 'U', 0, 1) }}
                            </div>
                            <span>{{ $course->creator->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-secondary" style="font-size: 12px;">{{ $course->created_at->format('M d, Y') }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/admin/courses/{{ $course->id }}/edit" class="btn-sm" style="background: #14b8a6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn-sm delete-btn"
                                    data-id="{{ $course->id }}"
                                    data-name="{{ $course->name }}"
                                    style="background: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-book"></i></div>
                        <h3>No Courses Found</h3>
                        <p>Get started by creating your first course.</p>
                        <a href="/admin/courses/create" class="add-btn" style="display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plus-circle"></i> Create New Course
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 0 10px;">
            <div style="color: #64748b; font-size: 14px;">
                Showing <span style="font-weight: 600;">{{ $courses->firstItem() }}</span>
                to <span style="font-weight: 600;">{{ $courses->lastItem() }}</span>
                of <span style="font-weight: 600;">{{ $courses->total() }}</span> courses
            </div>

            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                {{-- Previous Page Link --}}
                @if($courses->onFirstPage())
                    <span style="padding: 8px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8; cursor: not-allowed; font-size: 14px;">← Previous</span>
                @else
                    <a href="{{ $courses->previousPageUrl() }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">← Previous</a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $start = max(1, $courses->currentPage() - 2);
                    $end = min($courses->lastPage(), $courses->currentPage() + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $courses->url(1) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">1</a>
                    @if($start > 2)
                        <span style="padding: 8px 14px; color: #94a3b8;">...</span>
                    @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $courses->currentPage())
                        <span style="padding: 8px 14px; background: #667eea; border: 1px solid #667eea; border-radius: 8px; color: white; font-size: 14px; font-weight: 600;">{{ $page }}</span>
                    @else
                        <a href="{{ $courses->url($page) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">{{ $page }}</a>
                    @endif
                @endfor

                @if($end < $courses->lastPage())
                    @if($end < $courses->lastPage() - 1)
                        <span style="padding: 8px 14px; color: #94a3b8;">...</span>
                    @endif
                    <a href="{{ $courses->url($courses->lastPage()) }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">{{ $courses->lastPage() }}</a>
                @endif

                {{-- Next Page Link --}}
                @if($courses->hasMorePages())
                    <a href="{{ $courses->nextPageUrl() }}" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s;">Next →</a>
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
            <h3 class="modal-title" style="text-align: center;">Confirm Deletion</h3>
            <p class="modal-message" id="deleteMessage">Are you sure you want to delete this course? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="modal-btn modal-btn-secondary">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="modal-btn modal-btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Description Modal -->
    <div id="descriptionModal" class="modal">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h3 class="modal-title">Course Description</h3>
                <button onclick="closeDescriptionModal()" class="modal-close">×</button>
            </div>
            <div id="fullDescription" class="modal-body"></div>
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
            searchInput.placeholder = 'No courses to search';
            return;
        }

        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();

            rows.forEach(row => {
                // Skip the empty state row
                if (row.querySelector('td[colspan]')) return;

                const name = row.cells[1]?.querySelector('div')?.textContent.toLowerCase() || '';
                const code = row.cells[2]?.querySelector('span')?.textContent.toLowerCase() || '';

                if (name.includes(searchTerm) || code.includes(searchTerm)) {
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
                        <h3>No Matching Courses</h3>
                        <p>No courses found matching "${searchTerm}"</p>
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
                const courseId = this.dataset.id;
                const courseName = this.dataset.name;
                deleteForm.action = `/admin/courses/${courseId}`;
                deleteMessage.textContent = `Are you sure you want to delete "${courseName}"? This action cannot be undone.`;
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

    // Description modal functions
    window.showDescription = function(description) {
        const modal = document.getElementById('descriptionModal');
        const content = document.getElementById('fullDescription');
        content.textContent = description;
        modal.style.display = 'flex';
    }

    window.closeDescriptionModal = function() {
        document.getElementById('descriptionModal').style.display = 'none';
    }

    // Close description modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('descriptionModal');
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
@endpush