@extends('admin.layouts.master')

@section('title', 'Reports')
@section('page-icon', 'fa-chart-bar')
@section('page-title', 'Reports')

@section('content')
    <div style="text-align: center; padding: 60px 20px;">
        <div style="margin-bottom: 40px;">
            <i class="fas fa-chart-line" style="font-size: 4rem; color: #667eea;"></i>
        </div>
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Select a Report</h2>
        <p style="color: #64748b; margin-bottom: 40px;">Choose from the dropdown below to generate detailed reports</p>
        
        <div style="display: inline-block; position: relative;">
            <div class="dropdown" style="position: relative; display: inline-block;">
                <button onclick="toggleDropdown()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 32px; border: none; border-radius: 50px; font-size: 1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-chart-bar"></i> Generate Report
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="dropdownMenu" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); min-width: 220px; overflow: hidden; z-index: 1000;">
                    <a href="{{ url('/admin/reports/courses') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-bottom: 1px solid #e2e8f0;">
                        <i class="fas fa-book" style="color: #3b82f6; width: 24px;"></i> Courses Report
                    </a>
                    <a href="{{ url('/admin/reports/units') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-bottom: 1px solid #e2e8f0;">
                        <i class="fas fa-layer-group" style="color: #10b981; width: 24px;"></i> Units Report
                    </a>
                    <a href="{{ url('/admin/reports/lecturers') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-bottom: 1px solid #e2e8f0;">
                        <i class="fas fa-chalkboard-user" style="color: #f59e0b; width: 24px;"></i> Lecturers Report
                    </a>
                    <a href="{{ url('/admin/reports/students') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-bottom: 1px solid #e2e8f0;">
                        <i class="fas fa-user-graduate" style="color: #8b5cf6; width: 24px;"></i> Students Report
                    </a>
                    <a href="{{ url('/admin/reports/forum') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-bottom: 1px solid #e2e8f0;">
                        <i class="fas fa-comments" style="color: #ec489a; width: 24px;"></i> Forum Activity Report
                    </a>
                    <a href="{{ url('/admin/reports/flags') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s;">
                        <i class="fas fa-flag" style="color: #ef4444; width: 24px;"></i> Flags Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dropdown-menu a:hover {
            background: #f8fafc;
        }
    </style>

    <script>
        function toggleDropdown() {
            var menu = document.getElementById('dropdownMenu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.dropdown')) {
                var menu = document.getElementById('dropdownMenu');
                if (menu) menu.style.display = 'none';
            }
        }
    </script>
@endsection