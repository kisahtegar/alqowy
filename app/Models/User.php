<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * Represents a user in the application. The User model handles
 * authentication, role management, and various relationships such
 * as enrolled courses and subscription transactions. It includes
 * support for soft deletion, notification sending, and role assignment.
 *
 * @property string $name The user's full name
 * @property string $email The user's email address
 * @property string $password The user's hashed password
 * @property string|null $occupation The user's occupation (optional)
 * @property string|null $avatar The URL or path to the user's avatar image (optional)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'occupation',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the courses the user is enrolled in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses() 
    {
        return $this->belongsToMany(Course::class, 'course_students');
    }

    /**
     * Get the subscription transactions for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribe_transactions()
    {
        return $this->hasMany(SubscribeTransaction::class);
    }

    /**
     * Check if the user has an active subscription.
     *
     * This method calculates whether the user has an active, paid subscription
     * based on the most recent subscription and its end date.
     *
     * @return bool True if the user has an active subscription, false otherwise
     */
    public function hasActiveSubscriptions()
    {
        // Retrieve the most recent paid subscription transaction for the user
        $latestSubscription = $this->subscribe_transactions()
            ->where("is_paid", true)
            ->latest("updated_at")
            ->first();

        // If no subscription was found, the user does not have an active subscription
        if (!$latestSubscription) {
            return false;
        }

        // Calculate the end date of the current subscription
        $subscriptionEndDate = Carbon::parse(
            $latestSubscription->subscription_start_date
        )->addMonths(1);

        // Check if the current date is before or on the subscription's end date
        return Carbon::now()->lessThanOrEqualTo($subscriptionEndDate);
    }
}
