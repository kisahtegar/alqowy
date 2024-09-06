<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\SubscribeTransaction;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with summary statistics.
     *
     * This function retrieves and prepares the data for displaying the dashboard. It checks the role of the
     * currently authenticated user to determine which courses to include in the statistics. If the user is a
     * teacher, it filters the courses to include only those assigned to the current teacher and counts the 
     * number of distinct students enrolled in those courses. For other users, it counts the total number of 
     * distinct students. The function also retrieves counts for categories, transactions, and teachers.
     *
     * The collected data is then passed to the `dashboard` view for rendering.
     *
     * @return \Illuminate\View\View The view rendering the dashboard with summary statistics.
     */
    public function index() 
    {
        // Get the currently authenticated user
        $user = Auth::user();
        
        // Create a query for retrieving courses
        $coursesQuery = Course::query();

        // Check if the user has the 'teacher' role
        if ($user->hasRole('teacher')) {
            // Filter courses to include only those assigned to the current teacher
            $coursesQuery->whereHas('teacher', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
            // Count distinct students enrolled in the filtered courses
            $students = CourseStudent::whereIn('course_id', $coursesQuery->select('id'))
                ->distinct('user_id')
                ->count('user_id');
        } else {
            // Count distinct students across all courses for other users
            $students = CourseStudent::distinct('user_id')
                ->count('user_id');
        }

        // Count the total number of courses
        $courses = $coursesQuery->count();

        // Count the total number of categories, transactions, and teachers
        $categories = Category::count();
        $transactions = SubscribeTransaction::count();
        $teachers = Teacher::count();

        // Pass the collected data to the 'dashboard' view
        return view('dashboard', compact('categories', 'courses', 'transactions', 'students', 'teachers'));
    }
}
