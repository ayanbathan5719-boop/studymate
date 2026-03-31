@extends('lecturer.layouts.master')

@section('title', 'Unit Report PDF')
@section('page-title', 'Unit Report PDF')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unit Report - {{ $unit->code }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        h1 {
            color: #1e3a8a;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 8px;
            font-size: 22px;
            margin-bottom: 15px;
        }
        h2 {
            color: #2563eb;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        h3 {
            color: #475569;
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        .info-box {
            background: #f8fafc;
            border-left: 4px solid #f59e0b;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: 600;
            color: #475569;
            display: inline-block;
            width: 100px;
        }
        .info-value {
            color: #1e293b;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 12px;
        }
        .stat-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        th {
            background: #1e3a8a;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-pdf {
            background: #f59e0b;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 9px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>
        Unit Report: {{ $unit->code }} - {{ $unit->name }}
        <span class="badge-pdf">PDF Report</span>
    </h1>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Generated:</span>
            <span class="info-value">{{ $generatedAt }}</span>
        </div>
        @if($dateFrom || $dateTo)
        <div class="info-row">
            <span class="info-label">Date Range:</span>
            <span class="info-value">{{ $dateFrom ?: 'All' }} to {{ $dateTo ?: 'All' }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Course:</span>
            <span class="info-value">{{ $unit->course->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Students:</span>
            <span class="info-value">{{ $data['students']['total'] }} enrolled</span>
        </div>
    </div>

    <!-- Stats Overview -->
    <h2>📊 Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Students</div>
            <div class="stat-value">{{ $data['students']['total'] }}</div>
            <div class="stat-sub">{{ $data['students']['active'] }} active ({{ $data['students']['engagement_rate'] }}%)</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Resources</div>
            <div class="stat-value">{{ $data['resources']['total'] }}</div>
            <div class="stat-sub">{{ $data['resources']['total_downloads'] }} downloads</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Forum Posts</div>
            <div class="stat-value">{{ $data['forum']['total_posts'] }}</div>
            <div class="stat-sub">{{ $data['forum']['total_replies'] }} replies</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Study Time</div>
            <div class="stat-value">{{ $data['study_time']['total_hours'] }}h</div>
            <div class="stat-sub">{{ $data['study_time']['average_per_student'] }} min/student</div>
        </div>
    </div>

    <!-- Forum Activity -->
    <h2>💬 Forum Activity</h2>
    <table>
        <tr>
            <th>Metric</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Total Posts</td>
            <td><strong>{{ $data['forum']['total_posts'] }}</strong></td>
        </tr>
        <tr>
            <td>Posts by Lecturer</td>
            <td>{{ $data['forum']['posts_by_lecturer'] }}</td>
        </tr>
        <tr>
            <td>Posts by Students</td>
            <td>{{ $data['forum']['posts_by_students'] }}</td>
        </tr>
        <tr>
            <td>Total Replies</td>
            <td>{{ $data['forum']['total_replies'] }}</td>
        </tr>
        <tr>
            <td>Active Students in Forum</td>
            <td>{{ $data['forum']['active_students'] }}</td>
        </tr>
    </table>

    <!-- Resources -->
    <h2>📁 Resources</h2>
    
    <h3>Top Resources by Downloads</h3>
    <table>
        <thead>
            <tr>
                <th>Resource Title</th>
                <th>Downloads</th>
                <th>Uploaded</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['resources']['top_resources'] as $resource)
            <tr>
                <td>{{ $resource->title }}</td>
                <td>{{ $resource->download_count }}</td>
                <td>{{ $resource->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">No resources found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Resources by File Type</h3>
    <table>
        <thead>
            <tr>
                <th>File Type</th>
                <th>Count</th>
                <th>Downloads</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['resources']['by_type'] as $type => $typeData)
            <tr>
                <td><span class="badge">{{ strtoupper($type) }}</span></td>
                <td>{{ $typeData['count'] }}</td>
                <td>{{ $typeData['downloads'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Weekly Activity Summary -->
    <h2>📈 Weekly Activity (Last {{ count($data['weekly_activity']['dates']) }} days)</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Posts</th>
                <th>Replies</th>
                <th>Study Time (min)</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < count($data['weekly_activity']['dates']); $i++)
            <tr>
                <td>{{ $data['weekly_activity']['dates'][$i] }}</td>
                <td>{{ $data['weekly_activity']['posts'][$i] }}</td>
                <td>{{ $data['weekly_activity']['replies'][$i] }}</td>
                <td>{{ $data['weekly_activity']['study_time'][$i] }}</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        StudyMate - Lecturer Reports | Generated on {{ $generatedAt }}
    </div>
</body>
</html>
@endsection