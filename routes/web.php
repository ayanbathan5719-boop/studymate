<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LecturerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ✅ Updated root route with authentication check
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('lecturer')) {
            return redirect('/lecturer/dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect('/student/dashboard');
        }
    }

    return redirect('/login');
});

// ✅ Role-based dashboard redirect
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return redirect('/admin/dashboard');
    } elseif ($user->hasRole('lecturer')) {
        return redirect('/lecturer/dashboard');
    } elseif ($user->hasRole('student')) {
        return redirect('/student/dashboard');
    }

    return redirect('/');
})->middleware(['auth'])->name('dashboard');

// ✅ Forum routes (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/forum', [App\Http\Controllers\ForumController::class, 'index'])->name('forum.index');
});

// ✅ Shared Resource Routes (accessible by all authenticated users)
Route::middleware(['auth'])->prefix('resources')->name('resources.')->group(function () {
    Route::get('/', [App\Http\Controllers\ResourceController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ResourceController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ResourceController::class, 'store'])->name('store');
    Route::get('/{resource}', [App\Http\Controllers\ResourceController::class, 'show'])->name('show');
    Route::get('/{resource}/edit', [App\Http\Controllers\ResourceController::class, 'edit'])->name('edit');
    Route::put('/{resource}', [App\Http\Controllers\ResourceController::class, 'update'])->name('update');
    Route::delete('/{resource}', [App\Http\Controllers\ResourceController::class, 'destroy'])->name('destroy');
    Route::get('/{resource}/download', [App\Http\Controllers\ResourceController::class, 'download'])->name('download');
    Route::post('/track-download', [App\Http\Controllers\ResourceController::class, 'trackDownload'])->name('track-download');
    
    // ✅ Resource view route for tracking views
    Route::get('/view/{resource}', [App\Http\Controllers\ResourceController::class, 'view'])->name('view');

    // Resource comments
    Route::post('/{resource}/comment', [App\Http\Controllers\ResourceController::class, 'addComment'])->name('comment');
    Route::delete('/comment/{comment}', [App\Http\Controllers\ResourceController::class, 'deleteComment'])->name('comment.destroy');

    // API endpoints for dynamic loading
    Route::get('/api/units/{unitCode}/topics', [App\Http\Controllers\ResourceController::class, 'getTopicsForUnit'])->name('api.topics');
});

// ✅ Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // ========== FORUM ROUTES ==========
    Route::get('/admin/forum', [App\Http\Controllers\Admin\ForumController::class, 'index'])->name('admin.forum.index');
    Route::delete('/admin/forum/{post}', [App\Http\Controllers\Admin\ForumController::class, 'destroy'])->name('admin.forum.destroy');
    Route::post('/admin/forum/{post}/reply', [App\Http\Controllers\Admin\ForumController::class, 'reply'])->name('admin.forum.reply');
    Route::put('/admin/forum/{post}/toggle-pin', [App\Http\Controllers\Admin\ForumController::class, 'togglePin'])->name('admin.forum.toggle-pin');
    Route::put('/admin/forum/{post}/toggle-announcement', [App\Http\Controllers\Admin\ForumController::class, 'toggleAnnouncement'])->name('admin.forum.toggle-announcement');
    Route::delete('/admin/forum/reply/{reply}', [App\Http\Controllers\Admin\ForumController::class, 'deleteReply'])->name('admin.forum.delete-reply');
    Route::put('/admin/forum/reply/{reply}', [App\Http\Controllers\Admin\ForumController::class, 'updateReply'])->name('admin.forum.update-reply');
    
    // ========== REPORTS ROUTES ==========
    // Redirect /admin/reports to /admin/reports/courses
    Route::get('/admin/reports', function() {
        return redirect()->route('admin.reports.courses');
    })->name('admin.reports.index');
    
    Route::get('/admin/reports/courses', [App\Http\Controllers\Admin\ReportController::class, 'courses'])->name('admin.reports.courses');
    Route::get('/admin/reports/units', [App\Http\Controllers\Admin\ReportController::class, 'units'])->name('admin.reports.units');
    Route::get('/admin/reports/lecturers', [App\Http\Controllers\Admin\ReportController::class, 'lecturers'])->name('admin.reports.lecturers');
    Route::get('/admin/reports/students', [App\Http\Controllers\Admin\ReportController::class, 'students'])->name('admin.reports.students');
    Route::get('/admin/reports/forum', [App\Http\Controllers\Admin\ReportController::class, 'forum'])->name('admin.reports.forum');
    Route::get('/admin/reports/flags', [App\Http\Controllers\Admin\ReportController::class, 'flags'])->name('admin.reports.flags');
    Route::get('/admin/reports/export-csv', [App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('admin.reports.export-csv');
    Route::get('/admin/reports/export-pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('admin.reports.export-pdf');
    
    // Profile management
    Route::get('/admin/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/admin/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/admin/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('admin.profile.password.update');

    // Courses management
    Route::get('/admin/courses', [App\Http\Controllers\Admin\CourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/admin/courses/create', [App\Http\Controllers\Admin\CourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/admin/courses', [App\Http\Controllers\Admin\CourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/admin/courses/{course}/edit', [App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/admin/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/admin/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('admin.courses.destroy');

    // Units management
    Route::get('/admin/units', [App\Http\Controllers\Admin\UnitController::class, 'index'])->name('admin.units.index');
    Route::get('/admin/units/create', [App\Http\Controllers\Admin\UnitController::class, 'create'])->name('admin.units.create');
    Route::post('/admin/units', [App\Http\Controllers\Admin\UnitController::class, 'store'])->name('admin.units.store');
    Route::get('/admin/units/{unit}/edit', [App\Http\Controllers\Admin\UnitController::class, 'edit'])->name('admin.units.edit');
    Route::put('/admin/units/{unit}', [App\Http\Controllers\Admin\UnitController::class, 'update'])->name('admin.units.update');
    Route::delete('/admin/units/{unit}', [App\Http\Controllers\Admin\UnitController::class, 'destroy'])->name('admin.units.destroy');
    Route::get('/admin/units/assignments', [App\Http\Controllers\Admin\UnitController::class, 'assignments'])->name('admin.units.assignments');
    Route::put('/admin/units/{unit}/assign', [App\Http\Controllers\Admin\UnitController::class, 'assign'])->name('admin.units.assign');
    Route::put('/admin/units/{unit}/unassign', [App\Http\Controllers\Admin\UnitController::class, 'unassign'])->name('admin.units.unassign');

    // Lecturers management
    Route::get('/admin/lecturers', [App\Http\Controllers\LecturerController::class, 'index'])->name('admin.lecturers.index');
    Route::get('/admin/lecturers/create', [App\Http\Controllers\LecturerController::class, 'create'])->name('admin.lecturers.create');
    Route::post('/admin/lecturers', [App\Http\Controllers\LecturerController::class, 'store'])->name('admin.lecturers.store');
    Route::get('/admin/lecturers/{lecturer}/edit', [App\Http\Controllers\LecturerController::class, 'edit'])->name('admin.lecturers.edit');
    Route::put('/admin/lecturers/{lecturer}', [App\Http\Controllers\LecturerController::class, 'update'])->name('admin.lecturers.update');
    Route::delete('/admin/lecturers/{lecturer}', [App\Http\Controllers\LecturerController::class, 'destroy'])->name('admin.lecturers.destroy');
    Route::get('/admin/lecturers/{lecturer}/units', [App\Http\Controllers\Admin\LecturerUnitController::class, 'edit'])->name('admin.lecturers.units');
    Route::post('/admin/lecturers/{lecturer}/units', [App\Http\Controllers\Admin\LecturerUnitController::class, 'update'])->name('admin.lecturers.units.update');

    // Students management
    Route::get('/admin/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/pending-requests', [App\Http\Controllers\Admin\StudentController::class, 'pendingRequests'])->name('admin.students.pending-requests');
    Route::post('/admin/students/enrollments/{enrollment}/approve', [App\Http\Controllers\Admin\StudentController::class, 'approveEnrollment'])->name('admin.students.approve-enrollment');
    Route::post('/admin/students/enrollments/{enrollment}/reject', [App\Http\Controllers\Admin\StudentController::class, 'rejectEnrollment'])->name('admin.students.reject-enrollment');
    Route::get('/admin/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('admin.students.show');
    Route::delete('/admin/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('admin.students.destroy');

    // Flag management
    Route::get('/admin/flags', [App\Http\Controllers\Admin\FlagController::class, 'index'])->name('admin.flags.index');
    Route::get('/admin/flags/user/{user}', [App\Http\Controllers\Admin\FlagController::class, 'userFlags'])->name('admin.flags.user');
    Route::get('/admin/flags/{flag}/edit', [App\Http\Controllers\Admin\FlagController::class, 'edit'])->name('admin.flags.edit');
    Route::put('/admin/flags/{flag}', [App\Http\Controllers\Admin\FlagController::class, 'update'])->name('admin.flags.update');
    Route::delete('/admin/flags/{flag}', [App\Http\Controllers\Admin\FlagController::class, 'destroy'])->name('admin.flags.destroy');
    Route::post('/admin/flags/bulk-update', [App\Http\Controllers\Admin\FlagController::class, 'bulkUpdate'])->name('admin.flags.bulk-update');

    // Audit logs
    Route::get('/admin/audit', [App\Http\Controllers\Admin\AuditController::class, 'index'])->name('admin.audit.index');
    Route::get('/admin/audit/{auditLog}', [App\Http\Controllers\Admin\AuditController::class, 'show'])->name('admin.audit.show');
    Route::get('/admin/audit/export/csv', [App\Http\Controllers\Admin\AuditController::class, 'export'])->name('admin.audit.export');

    // Resources Management
    Route::get('/admin/resources', [App\Http\Controllers\Admin\ResourceController::class, 'index'])->name('admin.resources.index');
    Route::get('/admin/resources/{resource}', [App\Http\Controllers\Admin\ResourceController::class, 'show'])->name('admin.resources.show');
    Route::delete('/admin/resources/{resource}', [App\Http\Controllers\Admin\ResourceController::class, 'destroy'])->name('admin.resources.destroy');
    Route::get('/admin/resources/{resource}/download', [App\Http\Controllers\Admin\ResourceController::class, 'download'])->name('admin.resources.download');
});

// ✅ Lecturer routes
Route::prefix('lecturer')->name('lecturer.')->middleware(['auth', 'role:lecturer'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Lecturer\DashboardController::class, 'index'])->name('dashboard');

    // Units
    Route::get('/units', [App\Http\Controllers\Lecturer\UnitController::class, 'index'])->name('units.index');

    // ===== NEW: Unit Resources (Lecturer) =====
    Route::get('/units/{unit}/resources', [App\Http\Controllers\Lecturer\UnitController::class, 'resources'])->name('units.resources');

    // Resource Management (Lecturer specific views)
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [App\Http\Controllers\Lecturer\ResourceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Lecturer\ResourceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Lecturer\ResourceController::class, 'store'])->name('store');
        Route::get('/{resource}', [App\Http\Controllers\Lecturer\ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/edit', [App\Http\Controllers\Lecturer\ResourceController::class, 'edit'])->name('edit');
        Route::put('/{resource}', [App\Http\Controllers\Lecturer\ResourceController::class, 'update'])->name('update');
        Route::delete('/{resource}', [App\Http\Controllers\Lecturer\ResourceController::class, 'destroy'])->name('destroy');
        Route::get('/{resource}/download', [App\Http\Controllers\Lecturer\ResourceController::class, 'download'])->name('download');
        
        // ✅ AJAX endpoint for topics
        Route::get('/topics/{unitCode}', [App\Http\Controllers\Lecturer\ResourceController::class, 'getTopics'])->name('resources.topics');
        
        // ✅ NEW: Get unit resources for lecturer
        Route::get('/unit/{unitCode}/resources', [App\Http\Controllers\Lecturer\ResourceController::class, 'getUnitResources'])->name('resources.unit-resources');
    });

    // Deadline Management
    Route::resource('deadlines', App\Http\Controllers\Lecturer\DeadlineController::class);

    // Lecturer Forum Routes
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [App\Http\Controllers\Lecturer\ForumController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Lecturer\ForumController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Lecturer\ForumController::class, 'store'])->name('store');
        Route::get('/flagged', [App\Http\Controllers\Lecturer\ForumController::class, 'flaggedPosts'])->name('flagged');
        Route::get('/{post}', [App\Http\Controllers\Lecturer\ForumController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [App\Http\Controllers\Lecturer\ForumController::class, 'edit'])->name('edit');
        Route::put('/{post}', [App\Http\Controllers\Lecturer\ForumController::class, 'update'])->name('update');
        Route::delete('/{post}', [App\Http\Controllers\Lecturer\ForumController::class, 'destroy'])->name('delete');
        Route::post('/{post}/toggle-pin', [App\Http\Controllers\Lecturer\ForumController::class, 'togglePin'])->name('toggle-pin');
        Route::post('/{post}/toggle-announcement', [App\Http\Controllers\Lecturer\ForumController::class, 'toggleAnnouncement'])->name('toggle-announcement');
        Route::post('/{post}/reply', [App\Http\Controllers\Lecturer\ForumController::class, 'reply'])->name('reply');
        Route::post('/{post}/flag', [App\Http\Controllers\Lecturer\ForumController::class, 'flag'])->name('flag');
        Route::delete('/reply/{reply}', [App\Http\Controllers\Lecturer\ForumController::class, 'deleteReply'])->name('delete-reply');
        Route::post('/reply/{reply}/edit', [App\Http\Controllers\Lecturer\ForumController::class, 'editReply'])->name('edit-reply');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Lecturer\ReportController::class, 'index'])->name('index');
        Route::get('/export/pdf', [App\Http\Controllers\Lecturer\ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/csv', [App\Http\Controllers\Lecturer\ReportController::class, 'exportCsv'])->name('export-csv');
    });

    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Lecturer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\Lecturer\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [App\Http\Controllers\Lecturer\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/profile/password', [App\Http\Controllers\Lecturer\ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/password', [App\Http\Controllers\Lecturer\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ✅ Lecturer Topics Management
    Route::prefix('topics')->name('topics.')->group(function () {
        Route::get('/{unit}', [App\Http\Controllers\Lecturer\TopicController::class, 'index'])->name('index');
        Route::get('/{unit}/create', [App\Http\Controllers\Lecturer\TopicController::class, 'create'])->name('create');
        Route::post('/{unit}', [App\Http\Controllers\Lecturer\TopicController::class, 'store'])->name('store');
        Route::get('/{unit}/{topic}/edit', [App\Http\Controllers\Lecturer\TopicController::class, 'edit'])->name('edit');
        Route::put('/{unit}/{topic}', [App\Http\Controllers\Lecturer\TopicController::class, 'update'])->name('update');
        Route::delete('/{unit}/{topic}', [App\Http\Controllers\Lecturer\TopicController::class, 'destroy'])->name('destroy');
        Route::post('/reorder/{unit}', [App\Http\Controllers\Lecturer\TopicController::class, 'reorder'])->name('reorder');
        Route::post('/{unit}/{topic}/toggle', [App\Http\Controllers\Lecturer\TopicController::class, 'toggleStatus'])->name('toggle');
    });
});

// ✅ Student routes
Route::middleware(['auth', 'role:student'])->group(function () {
    // Dashboard
    Route::get('/student/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('student.dashboard');

    // Student Profile
    Route::get('/student/profile', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit');
    Route::post('/student/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
    Route::post('/student/profile/avatar', [App\Http\Controllers\Student\ProfileController::class, 'updateAvatar'])->name('student.profile.avatar');
    Route::post('/student/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('student.profile.password.update');

    // Unit Enrollment
    Route::get('/student/units/available', [App\Http\Controllers\Student\UnitController::class, 'available'])->name('student.units.available');
    Route::post('/student/units/enroll', [App\Http\Controllers\Student\UnitController::class, 'enroll'])->name('student.units.enroll');
    Route::delete('/student/units/{unit}/unenroll', [App\Http\Controllers\Student\UnitController::class, 'unenroll'])->name('student.units.unenroll');
    Route::get('/student/units/requests', [App\Http\Controllers\Student\UnitController::class, 'myRequests'])->name('student.units.requests');
    
    // ✅ Unit Show (Study Hub)
    Route::get('/student/units/{unit}', [App\Http\Controllers\Student\UnitController::class, 'show'])->name('student.units.show');

    // Student Resources - UPDATED with all tracking routes including mark-studied
    Route::prefix('resources')->name('student.resources.')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ResourceController::class, 'index'])->name('index');
        Route::get('/{resource}', [App\Http\Controllers\Student\ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/download', [App\Http\Controllers\Student\ResourceController::class, 'download'])->name('download');
        Route::post('/{resource}/mark-studied', [App\Http\Controllers\Student\ResourceController::class, 'markStudied'])->name('mark-studied');
        Route::post('/{resource}/save-notes', [App\Http\Controllers\Student\ResourceController::class, 'saveNotes'])->name('save-notes');
        Route::post('/track-download', [App\Http\Controllers\Student\ResourceController::class, 'trackDownload'])->name('track-download');
        
        // ✅ Resource tracking routes
        Route::post('/{resource}/track-view', [App\Http\Controllers\Student\ResourceController::class, 'trackView'])->name('track-view');
        Route::get('/viewer/{resource}', [App\Http\Controllers\Student\ResourceController::class, 'viewer'])->name('viewer');
        Route::get('/view/{resource}', [App\Http\Controllers\Student\ResourceController::class, 'view'])->name('view');
        
        // ✅ Save resource to unit
        Route::post('/{resource}/save-to-unit', [App\Http\Controllers\Student\ResourceController::class, 'saveToUnit'])->name('save-to-unit');
        
        // ===== NEW: Resume and Restart Study Routes =====
        Route::post('/{resource}/resume-study', [App\Http\Controllers\Student\ResourceController::class, 'resumeStudy'])->name('resume-study');
        Route::post('/{resource}/restart-study', [App\Http\Controllers\Student\ResourceController::class, 'restartStudy'])->name('restart-study');
    });

    // ===== NEW: Unit Resources (Student) =====
    // This route MUST be AFTER the resources prefix group to avoid conflicts
    Route::get('/student/units/{unit}/resources', [App\Http\Controllers\Student\UnitController::class, 'resources'])->name('student.units.resources');

    // Student Deadlines
    Route::get('/student/deadlines', [App\Http\Controllers\Student\DeadlineController::class, 'index'])->name('student.deadlines.index');
    Route::post('/student/deadlines/{deadline}/accept', [App\Http\Controllers\Student\DeadlineController::class, 'accept'])->name('student.deadlines.accept');
    Route::post('/student/deadlines/{deadline}/decline', [App\Http\Controllers\Student\DeadlineController::class, 'decline'])->name('student.deadlines.decline');
    Route::post('/student/deadlines/{deadline}/complete', [App\Http\Controllers\Student\DeadlineController::class, 'complete'])->name('student.deadlines.complete');
    Route::get('/student/deadlines/personal/create', [App\Http\Controllers\Student\DeadlineController::class, 'createPersonal'])->name('student.deadlines.personal');
    Route::post('/student/deadlines/personal', [App\Http\Controllers\Student\DeadlineController::class, 'storePersonal'])->name('student.deadlines.personal.store');

    // Student Forum Routes
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ForumController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Student\ForumController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Student\ForumController::class, 'store'])->name('store');
        Route::get('/{post}/edit', [App\Http\Controllers\Student\ForumController::class, 'edit'])->name('edit');
        Route::get('/{post}', [App\Http\Controllers\Student\ForumController::class, 'show'])->name('show');
        Route::put('/{post}', [App\Http\Controllers\Student\ForumController::class, 'update'])->name('update');
        Route::delete('/{post}', [App\Http\Controllers\Student\ForumController::class, 'destroy'])->name('delete');
        Route::post('/{post}/reply', [App\Http\Controllers\Student\ForumController::class, 'reply'])->name('reply');
        Route::post('/{post}/flag', [App\Http\Controllers\Student\ForumController::class, 'flag'])->name('flag');
        Route::delete('/reply/{reply}', [App\Http\Controllers\Student\ForumController::class, 'deleteReply'])->name('delete-reply');
        Route::put('/reply/{reply}', [App\Http\Controllers\Student\ForumController::class, 'editReply'])->name('edit-reply');
        Route::get('/unit-resources/{unitCode}', [App\Http\Controllers\Student\ForumController::class, 'getUnitResources'])->name('unit-resources');
    });

    // Student Calendar
    Route::get('/student/calendar', [App\Http\Controllers\Student\CalendarController::class, 'index'])->name('student.calendar.index');
    Route::get('/student/calendar/events', [App\Http\Controllers\Student\CalendarController::class, 'events'])->name('student.calendar.events');

    // Student Reports
    Route::get('/student/reports', [App\Http\Controllers\Student\ReportController::class, 'index'])->name('student.reports.index');

    // Custom Units
    Route::prefix('custom')->name('custom.')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\CustomUnitController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Student\CustomUnitController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Student\CustomUnitController::class, 'store'])->name('store');
        Route::get('/{customUnit}/edit', [App\Http\Controllers\Student\CustomUnitController::class, 'edit'])->name('edit');
        Route::put('/{customUnit}', [App\Http\Controllers\Student\CustomUnitController::class, 'update'])->name('update');
        Route::delete('/{customUnit}', [App\Http\Controllers\Student\CustomUnitController::class, 'destroy'])->name('destroy');
        Route::post('/{customUnit}/progress', [App\Http\Controllers\Student\CustomUnitController::class, 'updateProgress'])->name('progress');
    });

    // Unit Links (Study References)
    Route::prefix('links')->name('links.')->group(function () {
        Route::get('/{unit}', [App\Http\Controllers\Student\UnitLinkController::class, 'index'])->name('index');
        Route::get('/{unit}/create', [App\Http\Controllers\Student\UnitLinkController::class, 'create'])->name('create');
        Route::post('/{unit}', [App\Http\Controllers\Student\UnitLinkController::class, 'store'])->name('store');
        Route::get('/{unit}/{link}/edit', [App\Http\Controllers\Student\UnitLinkController::class, 'edit'])->name('edit');
        Route::put('/{unit}/{link}', [App\Http\Controllers\Student\UnitLinkController::class, 'update'])->name('update');
        Route::delete('/{unit}/{link}', [App\Http\Controllers\Student\UnitLinkController::class, 'destroy'])->name('destroy');
        Route::post('/{unit}/{link}/click', [App\Http\Controllers\Student\UnitLinkController::class, 'trackClick'])->name('click');
    });

    // Student Topics
    Route::prefix('topics')->name('topics.')->group(function () {
        Route::get('/{unit}', [App\Http\Controllers\Student\TopicController::class, 'index'])->name('index');
        Route::get('/{unit}/{topic}', [App\Http\Controllers\Student\TopicController::class, 'show'])->name('show');
        Route::post('/{unit}/{topic}/complete', [App\Http\Controllers\Student\TopicController::class, 'markComplete'])->name('complete');
        Route::get('/progress/{unit}', [App\Http\Controllers\Student\TopicController::class, 'progress'])->name('progress');
    });

    // Study Progress Routes
    Route::prefix('study')->name('study.')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\StudyProgressController::class, 'index'])->name('index');
        Route::get('/recommendations', [App\Http\Controllers\Student\StudyProgressController::class, 'recommendations'])->name('recommendations');
        Route::get('/statistics', [App\Http\Controllers\Student\StudyProgressController::class, 'statistics'])->name('statistics');
        Route::get('/unit/{unit}', [App\Http\Controllers\Student\StudyProgressController::class, 'unitProgress'])->name('unit');
        Route::get('/topic/{unit}/{topic}/start', [App\Http\Controllers\Student\StudyProgressController::class, 'startTopic'])->name('start');
        Route::post('/topic/{unit}/{topic}/progress', [App\Http\Controllers\Student\StudyProgressController::class, 'updateProgress'])->name('progress');
        Route::post('/topic/{unit}/{topic}/complete', [App\Http\Controllers\Student\StudyProgressController::class, 'completeTopic'])->name('complete');
    });
});

// ✅ Password Reset Routes (Laravel built-in)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
    ->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
    ->middleware('guest')->name('password.update');

// ✅ Notification routes (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'recent'])->name('recent');
        Route::post('/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/preferences', [App\Http\Controllers\NotificationController::class, 'getPreferences'])->name('preferences');
        Route::post('/preferences', [App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('update-preferences');
    });
});

// ✅ Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Fallback route for 403 errors
Route::get('/unauthorized', function () {
    return Inertia::render('Errors/403');
})->name('unauthorized');

require __DIR__ . '/auth.php';