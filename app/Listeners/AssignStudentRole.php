<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

/**
 * Class AssignStudentRole
 *
 * This class listens for the Registered event and assigns the 'student' role 
 * to the newly registered user. It ensures that every user who registers 
 * is automatically given the appropriate role based on their type.
 */
class AssignStudentRole
{
    /**
     * Handle the event.
     *
     * This method is called when the Registered event is fired. It takes 
     * the Registered event instance as a parameter and assigns the 'student' 
     * role to the user who has just registered.
     *
     * @param  Registered  $event  The event instance containing the registered user.
     * @return void
     */
    public function handle(Registered $event)
    {
        // Assign the 'student' role to the registered user
        $event->user->assignRole('student');
    }
}
