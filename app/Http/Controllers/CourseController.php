<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This function retrieves a paginated list of courses, with additional
     * related data such as categories, teachers, and students. If the
     * authenticated user is a teacher, the courses are filtered to show
     * only the ones they are responsible for. The results are ordered by 
     * the most recently added courses first.
     *
     * @return \Illuminate\Http\Response The view displaying the list of courses.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Create a query to fetch courses with related data
        $query = Course::with(['category', 'teacher', 'students'])->orderByDesc('id');

        // Restrict the query to the teacher's courses if the user has a 'teacher' role
        if ($user->hasRole('teacher')) {
            $query->whereHas('teacher', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        // Paginate the results to display 10 courses per page
        $courses = $query->paginate(10);

        // Return the view with the paginated list of courses
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * This function prepares the data required to display the course creation form.
     * It retrieves a list of available categories that the user can select for the course.
     *
     * @return \Illuminate\Http\Response The view displaying the course creation form.
     */
    public function create()
    {
        // Retrieve all categories to be displayed in the course creation form
        $categories = Category::all();

        // Return the view for creating a new course, passing the categories data
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * This function handles the creation of a new course. It validates the request data,
     * checks if the authenticated user is a valid teacher, and stores the course along with its keypoints.
     * It also handles the upload of a thumbnail image and generates a unique slug for the course.
     *
     * @param \App\Http\Requests\StoreCourseRequest $request The validated request object containing course data.
     * @return \Illuminate\Http\RedirectResponse A redirect to the courses index page upon successful creation.
     */
    public function store(StoreCourseRequest $request)
    {
        // Get the teacher associated with the current user
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();

        // Check if the user is a valid teacher
        if (!$teacher) {
            return redirect()->route('admin.courses.index')->withErrors('Unauthorized or invalid teacher.');
        }

        // Perform the course creation within a database transaction
        DB::transaction(function() use ($request, $teacher) {

            // Validate the incoming request data
            $validated = $request->validated();

            // Handle thumbnail file upload if provided
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            // Generate and set the slug based on the course name
            $validated['slug'] = Str::slug($validated['name']);

            // Associate the course with the teacher
            $validated['teacher_id'] = $teacher->id;

            // Create the new course in the database
            $course = Course::create($validated);

            // If course keypoints are provided, create them for the course
            if (!empty($validated['course_keypoints'])) {
                foreach ($validated['course_keypoints'] as $keypointText) {
                    $course->course_keypoints()->create([
                        'name' => $keypointText,
                    ]);
                }
            }
        });

        // Redirect to the courses index page after successful creation
        return redirect()->route('admin.courses.index');
    }

    /**
     * Display the specified resource.
     *
     * This function displays the details of a specific course. It retrieves the course
     * information and returns the view for showing the course details to the user.
     *
     * @param \App\Models\Course $course The course model instance to be displayed.
     * @return \Illuminate\View\View The view containing the course details.
     */
    public function show(Course $course)
    {
        // Return the view to display the course details
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * This function displays the form for editing a specific course. It retrieves
     * all categories to allow the user to assign or update the category of the course.
     *
     * @param \App\Models\Course $course The course model instance to be edited.
     * @return \Illuminate\View\View The view containing the edit form for the course.
     */
    public function edit(Course $course)
    {
        // Retrieve all categories to display in the edit form
        $categories = Category::all();

        // Return the view for editing the course
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * This function handles the update of a specific course's details. It validates
     * the incoming request, updates the course's data, including its thumbnail and key points,
     * and saves the changes within a database transaction.
     *
     * @param \App\Http\Requests\UpdateCourseRequest $request The validated request containing course update data.
     * @param \App\Models\Course $course The course model instance to be updated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the course's detail page upon success.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        DB::transaction(function() use ($request, $course) {
            // Validate and retrieve the updated course data
            $validated = $request->validated();

            // Handle thumbnail file upload if provided
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            // Generate and set the slug based on the course name
            $validated['slug'] = Str::slug($validated['name']);

            // Update the course with the validated data
            $course->update($validated);

            // Update course key points if provided
            if (!empty($validated['course_keypoints'])) {
                $course->course_keypoints()->delete();
                foreach ($validated['course_keypoints'] as $keypointText) {
                    $course->course_keypoints()->create(['name' => $keypointText]);
                }
            }
        });

        // Redirect to the course's detail page after successful update
        return redirect()->route('admin.courses.show', $course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * This function handles the deletion of a specific course. It attempts to delete the course
     * within a database transaction. If the deletion is successful, it commits the transaction and
     * redirects to the course index page. In case of an error, it rolls back the transaction and
     * redirects to the course index page with an error message.
     *
     * @param \App\Models\Course $course The course model instance to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects to the course index page with a success or error message.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();

        try {
            // Attempt to delete the course
            $course->delete();
            DB::commit();

            // Redirect to the course index page upon successful deletion
            return redirect()->route('admin.courses.index');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            
            // Redirect to the course index page with an error message
            return redirect()->route('admin.courses.index')->with('error', 'Terjadi kesalahan ketika menghapus data.');
        }
    }
}
