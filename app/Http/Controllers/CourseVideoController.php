<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseVideoRequest;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * This function displays the form for creating a new course video. It passes the specified course
     * to the view so that it can be associated with the new course video.
     *
     * @param \App\Models\Course $course The course instance to associate with the new course video.
     * @return \Illuminate\View\View The view for creating a new course video, with the course passed to it.
     */
    public function create(Course $course)
    {
        return view('admin.course_videos.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * This function handles the creation of a new course video. It validates the request data,
     * associates the new video with the specified course, and stores it in the database.
     * The function uses a database transaction to ensure that the operation is atomic,
     * meaning that if any part of the operation fails, no changes are saved to maintain data integrity.
     *
     * @param \App\Http\Requests\StoreCourseVideoRequest $request The validated request object containing the course video data.
     * @param \App\Models\Course $course The course instance to which the new video will be associated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the show page of the course where the new video was added.
     */
    public function store(StoreCourseVideoRequest $request, Course $course)
    {
        // Perform the course video creation within a database transaction
        DB::transaction(function() use ($request, $course) {

            // Validate the incoming request data
            $validated = $request->validated();

            // Associate the validated data with the given course
            $validated['course_id'] = $course->id;

            // Create the new course video in the database
            CourseVideo::create($validated);
        });

        // Redirect to the show page of the course after successful creation
        return redirect()->route('admin.courses.show', $course->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseVideo $courseVideo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * This function retrieves the course video that needs to be edited and returns the view
     * for editing it. The view is provided with the course video data, allowing users to
     * modify its details. This function does not perform any data changes but prepares the
     * necessary data for the editing view.
     *
     * @param \App\Models\CourseVideo $courseVideo The course video instance that is to be edited.
     * @return \Illuminate\View\View Returns the view for editing the course video, populated with the course video data.
     */
    public function edit(CourseVideo $courseVideo)
    {
        // Return the view for editing the course video, providing the course video instance for the form
        return view('admin.course_videos.edit', compact('courseVideo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * This function handles the updating of an existing course video. It validates the request data,
     * performs the update operation within a database transaction, and ensures that any changes are
     * saved correctly. The function updates the course video details based on the provided request data.
     *
     * @param \App\Http\Requests\StoreCourseVideoRequest $request The validated request object containing updated course video data.
     * @param \App\Models\CourseVideo $courseVideo The course video instance that is to be updated.
     * @return \Illuminate\Http\RedirectResponse A redirect to the course details page upon successful update.
     */
    public function update(StoreCourseVideoRequest $request, CourseVideo $courseVideo)
    {
        DB::transaction(function() use ($request, $courseVideo) {

            // Validate the incoming request data
            $validated = $request->validated();

            // Update the course video with the validated data
            $courseVideo->update($validated);

        });

        return redirect()->route('admin.courses.show', $courseVideo->course_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * This function handles the deletion of an existing course video. It performs the deletion operation
     * within a database transaction to ensure data integrity. If an error occurs during deletion, the transaction
     * is rolled back, and an error message is provided. Upon successful deletion, the user is redirected
     * to the course details page.
     *
     * @param \App\Models\CourseVideo $courseVideo The course video instance that is to be deleted.
     * @return \Illuminate\Http\RedirectResponse A redirect to the course details page upon successful deletion or with an error message if deletion fails.
     */
    public function destroy(CourseVideo $courseVideo)
    {
        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Delete the specified course video
            $courseVideo->delete();

            // Commit the transaction
            DB::commit();

            // Redirect to the course details page after successful deletion
            return redirect()->route('admin.courses.show', $courseVideo->course_id);
        } catch (\Throwable $th) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect to the course details page with an error message
            return redirect()->route('admin.courses.show', $courseVideo->course_id)->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
