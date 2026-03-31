<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentUnit;
use App\Models\Enrollment;
use App\Notifications\EnrollmentStatusNotification;  // ← ADD THIS LINE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = User::role('student')->with('enrolledUnits');
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by course/unit (if needed)
        if ($request->has('unit_id') && $request->unit_id) {
            $query->whereHas('enrolledUnits', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }
        
        $students = $query->paginate(10);
        
        // Get all units for filter dropdown
        $units = \App\Models\Unit::all();
        
        return view('admin.students.index', [
            'students' => $students,
            'units' => $units,
            'filters' => $request->only(['search', 'unit_id'])
        ]);
    }

    /**
     * Show student details.
     */
    public function show($id)
    {
        $student = User::findOrFail($id);
        
        // Ensure user is a student
        if (!$student->hasRole('student')) {
            abort(404);
        }
        
        $enrollments = Enrollment::where('student_id', $id)
                                 ->with('unit')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return view('admin.students.show', [
            'student' => $student,
            'enrollments' => $enrollments
        ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(User $student)
    {
        if (!$student->hasRole('student')) {
            return back()->with('error', 'User is not a student.');
        }
        
        $student->delete();
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    // ========== ENROLLMENT MANAGEMENT METHODS ==========

    /**
     * Display pending enrollment requests.
     */
    public function pendingRequests()
    {
        $pendingEnrollments = Enrollment::with(['student', 'unit'])
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        return view('admin.students.pending-requests', compact('pendingEnrollments'));
    }

    /**
     * Approve an enrollment request with notification.
     */
    public function approveEnrollment($enrollmentId)
    {
        try {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            
            // Check if enrollment is already processed
            if ($enrollment->status !== 'pending') {
                return redirect()->back()->with('error', 'This enrollment request has already been processed.');
            }
            
            // Approve the enrollment
            $enrollment->approve(Auth::id());

            // Send in-app notification to student
            if ($enrollment->student) {
                $enrollment->student->notify(new EnrollmentStatusNotification($enrollment, 'approved'));
            }

            return redirect()->back()->with('success', 'Enrollment request approved successfully. Student has been notified.');
            
        } catch (\Exception $e) {
            \Log::error('Enrollment approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve enrollment. Please try again.');
        }
    }

    /**
     * Reject an enrollment request with notification.
     */
    public function rejectEnrollment(Request $request, $enrollmentId)
    {
        try {
            $request->validate([
                'rejection_reason' => 'nullable|string|max:500'
            ]);

            $enrollment = Enrollment::findOrFail($enrollmentId);
            
            // Check if enrollment is already processed
            if ($enrollment->status !== 'pending') {
                return redirect()->back()->with('error', 'This enrollment request has already been processed.');
            }
            
            // Reject the enrollment
            $enrollment->reject($request->rejection_reason, Auth::id());

            // Send in-app notification to student
            if ($enrollment->student) {
                $enrollment->student->notify(new EnrollmentStatusNotification(
                    $enrollment, 
                    'rejected', 
                    $request->rejection_reason
                ));
            }

            return redirect()->back()->with('success', 'Enrollment request rejected. Student has been notified.');
            
        } catch (\Exception $e) {
            \Log::error('Enrollment rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject enrollment. Please try again.');
        }
    }

    /**
     * Bulk approve multiple enrollment requests.
     */
    public function bulkApproveEnrollments(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id'
        ]);

        $approvedCount = 0;
        $failedCount = 0;

        foreach ($request->enrollment_ids as $enrollmentId) {
            try {
                $enrollment = Enrollment::find($enrollmentId);
                
                if ($enrollment && $enrollment->status === 'pending') {
                    $enrollment->approve(Auth::id());
                    
                    // Send notification
                    if ($enrollment->student) {
                        $enrollment->student->notify(new EnrollmentStatusNotification($enrollment, 'approved'));
                    }
                    
                    $approvedCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Bulk approval failed for enrollment ' . $enrollmentId . ': ' . $e->getMessage());
            }
        }

        $message = "{$approvedCount} enrollment(s) approved successfully.";
        if ($failedCount > 0) {
            $message .= " {$failedCount} enrollment(s) failed.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get enrollment statistics for admin dashboard.
     */
    public function enrollmentStats()
    {
        $stats = [
            'total_pending' => Enrollment::where('status', 'pending')->count(),
            'total_approved' => Enrollment::where('status', 'approved')->count(),
            'total_rejected' => Enrollment::where('status', 'rejected')->count(),
            'recent_requests' => Enrollment::with(['student', 'unit'])
                                        ->where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get()
        ];

        return response()->json($stats);
    }
}