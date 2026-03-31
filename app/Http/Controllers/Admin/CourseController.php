<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $courses = Course::with('creator')
            ->latest()
            ->paginate(10);
        
        return view('admin.courses.index', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
        ]);

        Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect('/admin/courses')->with('success', 'Course created successfully!');
    }

    /**
     * Show the form for editing a course.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', [
            'course' => $course
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
        ]);

        $course->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect('/admin/courses')->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        
        return redirect('/admin/courses')->with('success', 'Course deleted successfully!');
    }
}