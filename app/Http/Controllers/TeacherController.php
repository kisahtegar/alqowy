<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This function retrieves a list of all teachers, ordered by their ID in descending order,
     * and passes the data to the view for display. It is used to show the main listing page of 
     * teachers in the admin panel.
     *
     * @return \Illuminate\View\View A view displaying the list of teachers.
     */
    public function index()
    {
        // Retrieve all teachers ordered by ID in descending order
        $teachers = Teacher::orderBy('id', 'desc')->get();

        // Return the view with the list of teachers
        return view('admin.teachers.index', [
            'teachers' => $teachers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * This function returns the view for creating a new teacher. It displays a form where
     * an admin can input the necessary details to add a new teacher to the system.
     *
     * @return \Illuminate\View\View A view with the form for creating a new teacher.
     */
    public function create()
    {
        // Return the view with the form for creating a new teacher
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * This function handles the creation of a new teacher. It validates the incoming request,
     * checks if the provided email exists and is not already assigned to a teacher, and then
     * creates a new teacher record in the database. The function also updates the user's role 
     * and ensures that the email is properly assigned to the teacher role.
     *
     * @param \App\Http\Requests\StoreTeacherRequest $request The validated request object containing teacher data.
     * @return \Illuminate\Http\RedirectResponse A redirect to the teachers index page upon successful creation.
     */
    public function store(StoreTeacherRequest $request)
    {
        // Validate the request data
        $validated = $request->validated();

        // Find the user associated with the provided email
        $user = User::where('email', $validated['email'])->first();

        // If user not exists, go back
        if (!$user) {
            return back()->withErrors([
                'email' => 'Data tidak ditemukan'
            ]);
        }

        // Check if the user is already a teacher
        if ($user->hasRole('teacher')) {
            return back()->withErrors([
                'email' => 'Email tersebut telah terdaftar menjadi guru.'
            ]);
        }

        // Perform the teacher creation within a database transaction
        DB::transaction(function () use ($user, $validated) {
            
            // Add the user ID and activate the teacher
            $validated['user_id'] = $user->id;
            $validated['is_active'] = true;

            // Create the new teacher in the database
            Teacher::create($validated);

            // Update user roles: remove student role and assign teacher role
            if ($user->hasRole('student')) {
                $user->removeRole('student');
            }

            $user->assignRole('teacher');
        });

        // Redirect to the teachers index page after successful creation
        return redirect()->route('admin.teachers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * This function handles the deletion of a teacher. It attempts to delete the teacher record
     * from the database and then updates the associated user's roles. If any errors occur during
     * the deletion process, the transaction is rolled back and an exception is thrown with an 
     * error message. The user’s role is updated to 'student' upon successful deletion.
     *
     * @param \App\Models\Teacher $teacher The teacher model instance to be deleted.
     * @return \Illuminate\Http\RedirectResponse A redirect back to the previous page upon successful deletion.
     * @throws \Illuminate\Validation\ValidationException If an error occurs during the deletion process.
     */
    public function destroy(Teacher $teacher)
    {
        try {
            // Begin a transaction
            DB::beginTransaction();
            
            // Delete the teacher record
            $teacher->delete();

            // Find the associated user and update roles
            $user = User::find($teacher->user_id);
            $user->removeRole('teacher');
            $user->assignRole('student');

            // Commit the transaction
            DB::commit();

            // Redirect back to the previous page
            return redirect()->back();
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Throw a validation exception with error messages
            $error = ValidationException::withMessages([
                'system_error' => ['System error !', $e->getMessage()],
            ]);

            throw $error;
        }
    }
}
