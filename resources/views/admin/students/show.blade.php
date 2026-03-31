@extends('admin.layouts.master')

@section('title', 'Student Details')
@section('page-icon', 'fa-user-graduate')
@section('page-title', 'Student Details')

@push('styles')
<link rel="stylesheet" href="/css/admin/forms.css">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <x-admin-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['name' => 'Students', 'url' => '/admin/students'],
        ['name' => 'Details', 'url' => null],
    ]" />

    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Student Info Card -->
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 32px;">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <div>
                    <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin-bottom: 5px;">{{ $student->name }}</h2>
                    <p style="color: #64748b;"><i class="fas fa-envelope" style="margin-right: 8px;"></i> {{ $student->email }}</p>
                    <p style="color: #64748b; font-size: 14px;"><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Joined: {{ $student->created_at->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Enrolled Units -->
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">
                <i class="fas fa-book-open" style="margin-right: 8px; color: #667eea;"></i> Enrolled Units
            </h3>
            
            @if($student->enrolledUnits->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                    @foreach($student->enrolledUnits as $unit)
                        <div style="background: #f8fafc; border-radius: 8px; padding: 15px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <div>
                                    <h4 style="font-weight: 600; color: #1e293b;">{{ $unit->name }}</h4>
                                    <p style="color: #64748b; font-size: 13px;">{{ $unit->code }}</p>
                                </div>
                                <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 20px; font-size: 11px;">
                                    <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> {{ ucfirst($unit->pivot->status) }}
                                </span>
                            </div>
                            <p style="color: #475569; font-size: 13px; margin-bottom: 5px;">
                                <i class="fas fa-book" style="margin-right: 5px; width: 16px;"></i> <strong>Course:</strong> {{ $unit->course->name ?? 'N/A' }}
                            </p>
                            <p style="color: #475569; font-size: 13px;">
                                <i class="fas fa-chalkboard-user" style="margin-right: 5px; width: 16px;"></i> <strong>Lecturer:</strong> {{ $unit->lecturer->name ?? 'Not assigned' }}
                            </p>
                            @if($unit->pivot->enrolled_at)
                                <p style="color: #64748b; font-size: 12px; margin-top: 10px;">
                                    <i class="fas fa-clock" style="margin-right: 5px;"></i> Enrolled: {{ \Carbon\Carbon::parse($unit->pivot->enrolled_at)->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"><i class="fas fa-book-open"></i></div>
                    <p style="color: #64748b;">This student is not enrolled in any units yet.</p>
                </div>
            @endif

            <div style="margin-top: 30px; text-align: right;">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Students
                </a>
            </div>
        </div>
    </div>
@endsection