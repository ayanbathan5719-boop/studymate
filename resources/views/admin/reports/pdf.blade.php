<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { color: #1e3a8a; border-bottom: 2px solid #f59e0b; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #1e3a8a; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        .even { background: #f8fafc; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <h1>StudyMate - {{ $title }}</h1>
    
    <p><strong>Generated:</strong> {{ $generatedAt }}</p>
    @if($dateFrom || $dateTo)
        <p><strong>Date Range:</strong> {{ $dateFrom ?: 'All' }} to {{ $dateTo ?: 'All' }}</p>
    @endif
    <p><strong>Total Records:</strong> {{ $data->count() }}</p>
    
    <table>
        <thead>
            <tr>
                @if($reportType == 'courses')
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Code</th>
                    <th>Units</th>
                    <th>Created</th>
                @elseif($reportType == 'units')
                    <th>ID</th>
                    <th>Unit Name</th>
                    <th>Code</th>
                    <th>Course</th>
                    <th>Lecturer</th>
                    <th>Resources</th>
                    <th>Forum Posts</th>
                    <th>Created</th>
                @elseif($reportType == 'lecturers')
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Units</th>
                    <th>Joined</th>
                @elseif($reportType == 'students')
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled</th>
                    <th>Posts</th>
                    <th>Joined</th>
                @elseif($reportType == 'forum')
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Unit</th>
                    <th>Replies</th>
                    <th>Created</th>
                @elseif($reportType == 'flags')
                    <th>ID</th>
                    <th>Reporter</th>
                    <th>Reported</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Created</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr class="{{ $index % 2 == 0 ? 'even' : '' }}">
                    @if($reportType == 'courses')
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->units_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @elseif($reportType == 'units')
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->course->name ?? 'N/A' }}</td>
                        <td>{{ $item->lecturer->name ?? 'N/A' }}</td>
                        <td>{{ $item->resources_count }}</td>
                        <td>{{ $item->forum_posts_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @elseif($reportType == 'lecturers')
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->department ?? 'N/A' }}</td>
                        <td>{{ $item->units_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @elseif($reportType == 'students')
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->enrolled_units_count }}</td>
                        <td>{{ $item->forum_posts_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @elseif($reportType == 'forum')
                        <td>#{{ $item->id }}</td>
                        <td>{{ Str::limit($item->title, 40) }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->unit->name ?? 'N/A' }}</td>
                        <td>{{ $item->replies_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @elseif($reportType == 'flags')
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->reporter->name ?? 'N/A' }}</td>
                        <td>{{ $item->reportedUser->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($item->reason) }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        StudyMate - Generated on {{ $generatedAt }}
    </div>
</body>
</html>