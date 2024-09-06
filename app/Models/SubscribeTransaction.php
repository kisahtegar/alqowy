<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SubscribeTransaction Model
 *
 * Represents a subscription transaction in the application. This model
 * handles subscription-related attributes, including payment status and
 * proof of payment. It is associated with a user who made the transaction.
 *
 * @property float $total_amount The total amount of the subscription transaction
 * @property bool $is_paid Indicates whether the subscription transaction has been paid
 * @property int $user_id The ID of the user who made the subscription transaction
 * @property string|null $proof The proof of payment for the subscription transaction
 * @property \Illuminate\Support\Carbon|null $subscription_start_date The start date of the subscription
 */
class SubscribeTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total_amount',
        'is_paid',
        'user_id',
        'proof',
        'subscription_start_date',
    ];

    /**
     * Get the user associated with the subscription transaction.
     *
     * This defines a relationship indicating that each subscription transaction
     * belongs to a user. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
