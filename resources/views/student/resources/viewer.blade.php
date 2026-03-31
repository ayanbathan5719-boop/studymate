@extends('student.layouts.master')

@section('title', 'View Resource')
@section('page-icon', 'fa-file-alt')
@section('page-title', 'View Resource')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/student/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.resources.index') }}">Resources</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $resource->title }}</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    /* Full Screen Viewer - IMPROVED */
    .viewer-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f5f7fa;
        z-index: 1050;
        display: flex;
        flex-direction: column;
    }
    
    .viewer-header {
        background: white;
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .viewer-header h3 {
        margin: 0;
        font-size: 1.2rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .resource-meta {
        display: flex;
        gap: 20px;
        margin-top: 6px;
        font-size: 0.75rem;
        color: #64748b;
        flex-wrap: wrap;
    }
    
    .resource-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .tracking-info {
        background: #fef9e3;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        color: #b45309;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .viewer-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .btn-viewer {
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
    }
    
    .btn-download-resource {
        background: #3b82f6;
        color: white;
    }
    
    .btn-download-resource:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    
    .btn-save-to-resources {
        background: #10b981;
        color: white;
    }
    
    .btn-save-to-resources:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    
    .btn-close-viewer {
        background: #ef4444;
        color: white;
    }
    
    .btn-close-viewer:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }
    
    /* Resume Prompt */
    .resume-prompt-bar {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 12px 24px;
        margin: 12px 20px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .btn-resume-prompt {
        background: #f59e0b;
        color: white;
        border: none;
        padding: 6px 18px;
        border-radius: 30px;
        cursor: pointer;
        margin-right: 8px;
    }
    
    .btn-restart-prompt {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 6px 18px;
        border-radius: 30px;
        cursor: pointer;
    }
    
    /* Viewer Content - EXPANDED */
    .viewer-content {
        flex: 1;
        background: #eef2ff;
        padding: 20px;
        overflow: auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    
    .document-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        flex: 1;
    }
    
    .document-toolbar {
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .document-frame {
        flex: 1;
        width: 100%;
        border: none;
        min-height: 500px;
    }
    
    /* Media Preview Styles */
    .media-preview {
        text-align: center;
        padding: 60px;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .media-preview i {
        font-size: 4rem;
        margin-bottom: 20px;
    }
    
    .media-preview h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        color: #1e293b;
    }
    
    .media-preview p {
        color: #64748b;
        margin-bottom: 24px;
    }
    
    /* Office Preview Section */
    .office-preview {
        text-align: center;
        padding: 60px;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .office-preview i {
        font-size: 5rem;
        margin-bottom: 20px;
    }
    
    .office-preview h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        color: #1e293b;
    }
    
    .office-preview p {
        color: #64748b;
        margin-bottom: 24px;
    }
    
    .office-actions {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    /* Study Tips */
    .study-tips {
        position: relative;
        margin: 12px 20px 0 20px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .study-tips i {
        font-size: 1.2rem;
        color: #fbbf24;
    }
    
    .study-tips span {
        flex: 1;
        line-height: 1.4;
    }
    
    .close-tip {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 0 5px;
        transition: color 0.2s;
    }
    
    .close-tip:hover {
        color: white;
    }
    
    .toolbar-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    @media (max-width: 768px) {
        .viewer-header {
            flex-direction: column;
            text-align: center;
        }
        
        .viewer-actions {
            justify-content: center;
        }
        
        .document-toolbar {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="viewer-fullscreen">
    <!-- Header Section -->
    <div class="viewer-header">
        <div>
            <h3>
                <i class="fas fa-file-alt"></i>
                {{ $resource->title }}
            </h3>
            <div class="resource-meta">
                <span><i class="far fa-clock"></i> Uploaded {{ $resource->created_at->diffForHumans() }}</span>
                <span><i class="fas fa-download"></i> {{ number_format($resource->download_count) }} downloads</span>
                <span><i class="fas fa-eye"></i> {{ number_format($resource->views_count) }} views</span>
                <span class="tracking-info"><i class="fas fa-chart-line"></i> Studying: <strong id="studyTime">0:00</strong></span>
            </div>
        </div>
        <div class="viewer-actions">
            <a href="{{ route('student.resources.download', $resource) }}" class="btn-viewer btn-download-resource" id="downloadBtn">
                <i class="fas fa-download"></i> Download to Computer
            </a>
            <button class="btn-viewer btn-save-to-resources" onclick="saveToResources({{ $resource->id }})">
                <i class="fas fa-folder-open"></i> Save to Unit Resources
            </button>
            <a href="{{ route('student.resources.index') }}" class="btn-viewer btn-close-viewer">
                <i class="fas fa-arrow-left"></i> Back to Resources
            </a>
        </div>
    </div>

    <!-- Resume Prompt -->
    @if(auth()->user()->hasRole('student') && isset($hasProgress) && $hasProgress && $totalStudied > 0 && !session('resumed'))
    <div id="resumePrompt" class="resume-prompt-bar">
        <div>
            <i class="fas fa-clock"></i>
            <strong>Continue studying?</strong>
            <span>You previously studied this for <strong>{{ floor($totalStudied / 60) }}</strong> minutes</span>
        </div>
        <div>
            <button onclick="resumeStudy()" class="btn-resume-prompt">
                <i class="fas fa-play"></i> Resume
            </button>
            <button onclick="restartStudy()" class="btn-restart-prompt">
                <i class="fas fa-redo-alt"></i> Start Over
            </button>
        </div>
    </div>
    @endif

    <!-- Study Tips -->
    <div class="study-tips" id="studyTips">
        <i class="fas fa-lightbulb" id="tipIcon"></i>
        <span id="tipMessage">📖 Study Tip: Take notes while reading. Click "Save to Unit Resources" to keep this file for later study!</span>
        <button class="close-tip" onclick="closeTip()">×</button>
    </div>

    <!-- Viewer Content - Handles different file types -->
    <div class="viewer-content">
        <div class="document-container">
            @php
                $extension = strtolower(pathinfo($resource->file_name ?? $resource->title, PATHINFO_EXTENSION));
                $isWord = in_array($extension, ['doc', 'docx']);
                $isExcel = in_array($extension, ['xls', 'xlsx']);
                $isPowerPoint = in_array($extension, ['ppt', 'pptx', 'pps']);
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv']);
                $isPdf = $extension === 'pdf';
                $isLink = $resource->file_type === 'link';
                $fileUrl = asset('storage/' . $resource->file_path);
            @endphp
            
            @if($isLink)
                {{-- External Link - Open in new tab with tracking --}}
                <div class="media-preview">
                    <i class="fas fa-external-link-alt" style="color: #10b981;"></i>
                    <h3>External Resource</h3>
                    <p>This will open in a new tab. Your study time will be tracked.</p>
                    <a href="{{ $resource->url }}" target="_blank" class="btn-viewer btn-download-resource" id="externalLinkBtn" style="background: #10b981;">
                        <i class="fas fa-external-link-alt"></i> Open in New Tab
                    </a>
                </div>
                
            @elseif($isWord || $isExcel || $isPowerPoint)
                {{-- Office Documents - Show options to download or open in new tab --}}
                <div class="office-preview">
                    @if($isPowerPoint)
                        <i class="fas fa-file-powerpoint" style="color: #d04423;"></i>
                        <h3>PowerPoint Presentation</h3>
                    @elseif($isWord)
                        <i class="fas fa-file-word" style="color: #2b5797;"></i>
                        <h3>Word Document</h3>
                    @elseif($isExcel)
                        <i class="fas fa-file-excel" style="color: #217346;"></i>
                        <h3>Excel Spreadsheet</h3>
                    @endif
                    <p>This file cannot be previewed directly. Choose how you want to view it:</p>
                    <div class="office-actions">
                        <a href="{{ route('student.resources.download', $resource) }}" class="btn-viewer btn-download-resource" style="background: #3b82f6;" id="officeDownloadBtn">
                            <i class="fas fa-download"></i> Download to Computer
                        </a>
                        <a href="{{ $fileUrl }}" target="_blank" class="btn-viewer btn-download-resource" style="background: #10b981;" id="officeOpenBtn">
                            <i class="fas fa-external-link-alt"></i> Open in New Tab
                        </a>
                    </div>
                    <p style="margin-top: 20px; font-size: 0.8rem; color: #94a3b8;">
                        <i class="fas fa-info-circle"></i> Your study time will be tracked when you open the file.
                    </p>
                </div>
                
            @elseif($isImage)
                {{-- Image - Display directly with zoom controls --}}
                <div class="document-toolbar">
                    <div class="zoom-controls">
                        <button onclick="zoomImageOut()"><i class="fas fa-search-minus"></i></button>
                        <span id="zoomLevel">100%</span>
                        <button onclick="zoomImageIn()"><i class="fas fa-search-plus"></i></button>
                        <button onclick="resetImageZoom()"><i class="fas fa-sync-alt"></i> Reset</button>
                    </div>
                </div>
                <div style="flex: 1; overflow: auto; text-align: center; padding: 20px;">
                    <img id="previewImage" src="{{ $fileUrl }}" alt="{{ $resource->title }}" style="max-width: 100%; transition: transform 0.2s;">
                </div>
                
            @elseif($isVideo)
                {{-- Video - Use HTML5 video player --}}
                <video controls class="preview-video" style="width: 100%; max-height: 80vh; margin: 20px auto; display: block;">
                    <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                    Your browser does not support the video tag.
                </video>
                
            @elseif($isPdf)
                {{-- PDF - Use iframe with toolbar --}}
                <div class="document-toolbar">
                    <div class="page-controls">
                        <button onclick="prevPage()"><i class="fas fa-chevron-left"></i> Previous</button>
                        <span>Page <span id="currentPage">1</span> of <span id="totalPages">...</span></span>
                        <button onclick="nextPage()">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="zoom-controls">
                        <button onclick="zoomOut()"><i class="fas fa-search-minus"></i></button>
                        <span id="zoomLevel">100%</span>
                        <button onclick="zoomIn()"><i class="fas fa-search-plus"></i></button>
                        <button onclick="resetZoom()"><i class="fas fa-sync-alt"></i> Reset</button>
                    </div>
                </div>
                <iframe id="documentFrame" class="document-frame" src="{{ $fileUrl }}" title="{{ $resource->title }}"></iframe>
                
            @else
                {{-- Other files - Show download option --}}
                <div class="media-preview">
                    <i class="fas fa-file-alt" style="color: #64748b;"></i>
                    <h3>File Preview Not Available</h3>
                    <p>This file type cannot be previewed. Please download to view.</p>
                    <a href="{{ route('student.resources.download', $resource) }}" class="btn-viewer btn-download-resource">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // =============================================
    // CUMULATIVE TIME TRACKING
    // =============================================
    let currentSessionSeconds = 0;
    let totalSecondsStudied = {{ $totalStudied ?? 0 }};
    let hasExistingProgress = {{ isset($hasProgress) && $hasProgress ? 'true' : 'false' }};
    let timerInterval = null;
    let isTimerRunning = true;
    let resumePromptShown = {{ isset($hasProgress) && $hasProgress && $totalStudied > 0 && !session('resumed') ? 'true' : 'false' }};
    
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }
    
    function updateTimeDisplay() {
        const totalSeconds = totalSecondsStudied + currentSessionSeconds;
        const displayElement = document.getElementById('studyTime');
        if (displayElement) {
            displayElement.textContent = formatTime(totalSeconds);
        }
    }
    
    function saveProgress(isFinal = false) {
        const timeToAdd = currentSessionSeconds;
        if (timeToAdd === 0 && !isFinal) return;
        
        fetch('{{ route("student.resources.track-view", $resource) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ time_spent: timeToAdd })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.total_time_spent) {
                totalSecondsStudied = data.total_time_spent;
                if (!isFinal) {
                    currentSessionSeconds = 0;
                }
                updateTimeDisplay();
            }
        })
        .catch(error => console.error('Error saving progress:', error));
    }
    
    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            if (isTimerRunning) {
                currentSessionSeconds++;
                updateTimeDisplay();
                if (currentSessionSeconds % 30 === 0 && currentSessionSeconds > 0) {
                    saveProgress(false);
                }
            }
        }, 1000);
    }
    
    function resumeStudy() {
        fetch('{{ route("student.resources.resume-study", $resource) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        })
        .then(() => {
            document.getElementById('resumePrompt')?.remove();
            isTimerRunning = true;
            startTimer();
        });
    }
    
    function restartStudy() {
        if (confirm('Are you sure? This will reset your study time for this resource.')) {
            fetch('{{ route("student.resources.restart-study", $resource) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            })
            .then(() => {
                totalSecondsStudied = 0;
                currentSessionSeconds = 0;
                updateTimeDisplay();
                document.getElementById('resumePrompt')?.remove();
                isTimerRunning = true;
                startTimer();
            });
        }
    }
    
    function initializeTimer() {
        if (resumePromptShown) {
            isTimerRunning = false;
        } else {
            startTimer();
        }
    }
    
    initializeTimer();
    
    window.addEventListener('beforeunload', function() {
        if (currentSessionSeconds > 0) saveProgress(true);
    });
    
    // =============================================
    // PDF ZOOM AND PAGE FUNCTIONS
    // =============================================
    let currentZoom = 100;
    
    function zoomIn() {
        let frame = document.getElementById('documentFrame');
        if (frame && frame.tagName === 'IFRAME') {
            currentZoom = Math.min(currentZoom + 10, 200);
            applyZoom();
        }
    }
    
    function zoomOut() {
        let frame = document.getElementById('documentFrame');
        if (frame && frame.tagName === 'IFRAME') {
            currentZoom = Math.max(currentZoom - 10, 50);
            applyZoom();
        }
    }
    
    function resetZoom() {
        let frame = document.getElementById('documentFrame');
        if (frame && frame.tagName === 'IFRAME') {
            currentZoom = 100;
            applyZoom();
        }
    }
    
    function applyZoom() {
        let frame = document.getElementById('documentFrame');
        if (frame) {
            frame.style.transform = `scale(${currentZoom / 100})`;
            frame.style.transformOrigin = '0 0';
            frame.style.width = `${100 * 100 / currentZoom}%`;
            frame.style.height = `${100 * 100 / currentZoom}%`;
            document.getElementById('zoomLevel').textContent = currentZoom + '%';
        }
    }
    
    function nextPage() { 
        let frame = document.getElementById('documentFrame');
        if (frame) frame.contentWindow.postMessage({ type: 'nextPage' }, '*'); 
    }
    
    function prevPage() { 
        let frame = document.getElementById('documentFrame');
        if (frame) frame.contentWindow.postMessage({ type: 'prevPage' }, '*'); 
    }
    
    // =============================================
    // IMAGE ZOOM FUNCTIONS
    // =============================================
    let imageZoom = 100;
    let imageElement = document.getElementById('previewImage');
    
    function zoomImageIn() {
        if (imageElement) {
            imageZoom = Math.min(imageZoom + 10, 300);
            imageElement.style.transform = `scale(${imageZoom / 100})`;
            document.getElementById('zoomLevel').textContent = imageZoom + '%';
        }
    }
    
    function zoomImageOut() {
        if (imageElement) {
            imageZoom = Math.max(imageZoom - 10, 50);
            imageElement.style.transform = `scale(${imageZoom / 100})`;
            document.getElementById('zoomLevel').textContent = imageZoom + '%';
        }
    }
    
    function resetImageZoom() {
        if (imageElement) {
            imageZoom = 100;
            imageElement.style.transform = 'scale(1)';
            document.getElementById('zoomLevel').textContent = '100%';
        }
    }
    
    // Track initial view
    fetch('{{ route("student.resources.track-view", $resource) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ time_spent: 0 })
    });
    
    // Track download
    document.getElementById('downloadBtn').addEventListener('click', function(e) {
        fetch('{{ route("student.resources.track-download", $resource) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
        if (currentSessionSeconds > 0) {
            saveProgress(true);
        }
    });
    
    // Track external link clicks
    const externalLinkBtn = document.getElementById('externalLinkBtn');
    if (externalLinkBtn) {
        externalLinkBtn.addEventListener('click', function() {
            fetch('{{ route("student.resources.track-view", $resource) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    time_spent: currentSessionSeconds,
                    external_view: true 
                })
            });
            
            if (currentSessionSeconds > 0) {
                saveProgress(true);
            }
        });
    }
    
    // Track office document opens (download and open in new tab)
    const officeDownloadBtn = document.getElementById('officeDownloadBtn');
    const officeOpenBtn = document.getElementById('officeOpenBtn');
    
    if (officeDownloadBtn) {
        officeDownloadBtn.addEventListener('click', function() {
            if (currentSessionSeconds > 0) {
                saveProgress(true);
            }
        });
    }
    
    if (officeOpenBtn) {
        officeOpenBtn.addEventListener('click', function() {
            fetch('{{ route("student.resources.track-view", $resource) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    time_spent: currentSessionSeconds,
                    external_view: true 
                })
            });
            
            if (currentSessionSeconds > 0) {
                saveProgress(true);
            }
        });
    }
    
    // Save to Unit Resources
    function saveToResources(resourceId) {
        fetch('{{ route("student.resources.save-to-unit", $resource) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const btn = document.querySelector('.btn-save-to-resources');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
                setTimeout(() => { btn.innerHTML = originalText; }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Animated Study Tips
    let tipIndex = 0;
    let tipInterval;
    let tipVisible = true;
    
    const tips = [
        { icon: "fa-lightbulb", message: "📖 Study Tip: Take notes while reading. They help with retention!" },
        { icon: "fa-clock", message: "⏰ Study Tip: Take short breaks every 25 minutes (Pomodoro technique)!" },
        { icon: "fa-book-open", message: "📚 Study Tip: Highlight key points as you read!" },
        { icon: "fa-chart-line", message: "📊 Study Tip: Review your notes within 24 hours for better memory!" },
        { icon: "fa-folder-open", message: "📁 Study Tip: Save important resources to your unit folder for later!" },
        { icon: "fa-question-circle", message: "❓ Study Tip: Ask questions in the forum if something isn't clear!" }
    ];
    
    function showNextTip() {
        if (!tipVisible) return;
        const tipDiv = document.getElementById('studyTips');
        const tipIcon = document.getElementById('tipIcon');
        const tipMessage = document.getElementById('tipMessage');
        
        tipDiv.style.opacity = '0';
        setTimeout(() => {
            tipIndex = (tipIndex + 1) % tips.length;
            tipIcon.className = `fas ${tips[tipIndex].icon}`;
            tipMessage.innerHTML = tips[tipIndex].message;
            tipDiv.style.opacity = '1';
        }, 300);
    }
    
    tipInterval = setInterval(showNextTip, 12000);
    
    function closeTip() {
        tipVisible = false;
        const tipDiv = document.getElementById('studyTips');
        tipDiv.style.opacity = '0';
        tipDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => { tipDiv.style.display = 'none'; }, 500);
        clearInterval(tipInterval);
    }
</script>
@endpush
@endsection