<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * This function returns the view for editing the user's profile. It provides the current user's information
     * to the view so that it can be displayed and edited.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request object containing the current user's information.
     * @return \Illuminate\View\View The view for editing the user's profile.
     */
    public function edit(Request $request): View
    {
        // Return the view with the current user's information
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * This function updates the current user's profile information based on the validated request data. 
     * If the user's email is changed, the email verification timestamp is set to null. The user's 
     * profile is saved after updating.
     *
     * @param \App\Http\Requests\ProfileUpdateRequest $request The validated request object containing the new profile data.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the profile edit page with a status message indicating success.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Fill the user model with the validated data from the request
        $request->user()->fill($request->validated());

        // If the email has changed, set the email verification timestamp to null
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save the updated user profile
        $request->user()->save();

        // Redirect to the profile edit page with a success status message
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     *
     * This function handles the deletion of the current user's account. It first validates the request
     * to ensure the correct password is provided. If validation passes, the user is logged out, and their
     * account is deleted from the database. The user's session is then invalidated and a new session token
     * is generated to prevent reuse of the old session. Finally, the user is redirected to the homepage.
     *
     * @param \Illuminate\Http\Request $request The request object containing the user's current password for validation.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the homepage after successful account deletion.
     *
     * @throws \Illuminate\Validation\ValidationException If the password validation fails.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validate the request to ensure the current password is provided
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Retrieve the current authenticated user
        $user = $request->user();

        // Log out the user
        Auth::logout();

        // Delete the user's account
        $user->delete();

        // Invalidate the current session and regenerate the session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the homepage
        return Redirect::to('/');
    }
}
