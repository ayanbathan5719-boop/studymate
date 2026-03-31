<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Deadlines - StudyMate</title>
    <link rel="stylesheet" href="/css/student/deadlines.css">
</head>
<body>
    <div class="container">
        <h1>📅 My Deadlines</h1>
        <p class="subtitle">Manage your upcoming assignments, projects, and exams</p>
        
        <div class="deadlines-list">
            @php
                // Sample data - will be replaced with real data from controller
                $sampleDeadlines = [
                    (object)[
                        'id' => 1,
                        'title' => 'Web Development Assignment',
                        'unit_name' => 'BIT 2204: Web Development',
                        'type' => 'assignment',
                        'due_date' => now()->addDays(3),
                        'description' => 'Create a responsive website using HTML, CSS, and JavaScript',
                        'is_accepted' => false,
                        'is_completed' => false
                    ],
                    (object)[
                        'id' => 2,
                        'title' => 'Database Design Project',
                        'unit_name' => 'BIT 2102: Database Systems',
                        'type' => 'project',
                        'due_date' => now()->addDays(10),
                        'description' => 'Design and implement a database for a library system',
                        'is_accepted' => true,
                        'is_completed' => false
                    ]
                ];
            @endphp
            
            @forelse($sampleDeadlines as $deadline)
                <div class="deadline-card">
                    <div class="deadline-header">
                        <span class="deadline-title">{{ $deadline->title }}</span>
                        <span class="deadline-type type-{{ $deadline->type }}">{{ ucfirst($deadline->type) }}</span>
                    </div>
                    
                    <div class="deadline-unit">{{ $deadline->unit_name }}</div>
                    
                    <div class="deadline-details">
                        <div class="detail-item">📅 Due: {{ $deadline->due_date->format('M d, Y') }}</div>
                        <div class="detail-item">⏰ Time: {{ $deadline->due_date->format('h:i A') }}</div>
                        @if($deadline->due_date->diffInDays(now()) <= 3)
                            <div class="detail-item" style="color: #e53e3e;">⚠️ Urgent</div>
                        @endif
                    </div>
                    
                    <div class="deadline-description">{{ $deadline->description }}</div>
                    
                    <div class="deadline-actions">
                        @if($deadline->is_completed)
                            <button class="btn btn-completed" disabled>✅ Completed</button>
                        @elseif($deadline->is_accepted)
                            <button class="btn btn-complete">Mark as Complete</button>
                        @else
                            <button class="btn btn-accept">Accept Deadline</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-deadlines">
                    <p>No deadlines found for your enrolled units.</p>
                </div>
            @endforelse
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="/dashboard" style="color: #667eea; text-decoration: none;">← Back to Dashboard</a>
        </div>
    </div>
    
    <script src="/js/student/deadlines.js"></script>
</body>
</html>