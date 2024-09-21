<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Course;
use App\Models\SubscribeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * FrontController handles the main front-end interactions for the platform.
 * 
 * This controller is responsible for displaying courses, handling checkout transactions,
 * and managing the learning process. It includes various actions such as viewing course details,
 * processing payments, and restricting access to learning content based on user subscriptions.
 */
class FrontController extends Controller
{

    /**
     * Display the homepage with a list of courses.
     *
     * This function retrieves a list of courses from the database, along with their associated categories, 
     * teachers, and enrolled students. The courses are ordered in descending order of their IDs (newest first). 
     * The retrieved courses are then passed to the 'front.index' view for rendering the homepage.
     *
     * @return \Illuminate\View\View The view rendering the homepage with the list of courses.
     */
    public function index() 
    {
        // Retrieve all courses with their related categories, teachers, and students, ordered by newest
        $courses = Course::with(['category', 'teacher', 'students'])->orderByDesc('id')->get();

        // Pass the retrieved courses to the 'front.index' view
        return view('front.index', compact('courses'));
    }

    /**
     * Display the details of a specific course.
     *
     * This function shows detailed information about a given course. The course is passed to the view 
     * along with its associated data (such as category, teacher, students, etc.) for rendering on the course 
     * details page.
     *
     * @param \App\Models\Course $course The course whose details are to be displayed.
     * @return \Illuminate\View\View The view rendering the course details page.
     */
    public function details(Course $course) 
    {
        // Pass the course to the 'front.details' view to display its details
        return view('front.details', compact('course'));
    }

    /**
     * Display the pricing page.
     *
     * This function renders the pricing page where users can view subscription plans 
     * or payment-related information.
     *
     * @return \Illuminate\View\View The view rendering the pricing page.
     */
    public function pricing() 
    {
        // Return the 'front.pricing' view to display the pricing page
        return view('front.pricing');
    }

    /**
     * Display the checkout page.
     *
     * This function renders the checkout page, allowing users to finalize their subscription
     * and payment process.
     *
     * @return \Illuminate\View\View The view rendering the checkout page.
     */
    public function checkout() 
    {
        // Return the 'front.checkout' view to display the checkout page
        return view('front.checkout');
    }
    
    /**
     * Handle the subscription checkout process.
     *
     * This function processes the checkout request, ensuring that the user can only
     * proceed if they do not have any active subscriptions. It validates the request data,
     * manages the file upload (if applicable), and creates a new subscription transaction
     * within a database transaction for atomicity.
     *
     * @param  \App\Http\Requests\StoreSubscribeTransactionRequest  $request  The incoming request, containing the subscription data.
     * @return \Illuminate\Http\RedirectResponse  A redirect to the dashboard upon successful checkout.
     */
    public function checkout_store(StoreSubscribeTransactionRequest $request) 
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // If the user has an active subscription, redirect them to the index page
        if ($user->hasActiveSubscriptions()) {
            return redirect()->route('front.index');
        }

        // Use a database transaction to ensure atomicity
        DB::transaction(function() use ($request, $user) {

            // Retrieve validated data from the request
            $validated = $request->validated();

            // Check if an proof file is provided and handle the file upload
            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            // Assign the user ID and set the total amount and payment status
            $validated['user_id'] = $user->id;
            $validated['total_amount'] = 429000;
            $validated['is_paid'] = false;

            // Create the subscription transaction with the validated data
            $transaction = SubscribeTransaction::create($validated);
        });

        // Redirect to the dashboard upon successful transaction
        return redirect()->route('dashboard');
    }

    /**
     * Display the learning page for a specific course and video.
     *
     * This function verifies if the user has an active subscription before allowing them to access
     * the learning content. If they do, it retrieves the course and video details and syncs the course
     * to the user's enrolled courses list without detaching existing relationships.
     *
     * @param  \App\Models\Course  $course  The course the user is trying to access.
     * @param  int  $courseVideoId  The ID of the video the user is trying to watch.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View  Redirect to pricing page if no active subscription, or show the learning view.
     */
    public function learning(Course $course, $courseVideoId) 
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user has an active subscription
        if (!$user->hasActiveSubscriptions()) {
            // If not, redirect them to the pricing page
            return redirect()->route('front.pricing');
        }

        // Find the course video by ID
        $video = $course->course_videos->firstWhere('id', $courseVideoId);

        // Sync the course to the user's courses without detaching any existing course relationships
        $user->courses()->syncWithoutDetaching($course->id);

        // Return the learning view with the course and video data
        return view('front.learning', compact('course', 'video'));

    }
}
