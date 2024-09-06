<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Teacher Model
 *
 * Represents a teacher in the application. The Teacher model handles
 * teacher-specific attributes and relationships, such as the user account
 * it belongs to and the courses they teach. It supports soft deletion
 * and is associated with a user and multiple courses.
 *
 * @property int $user_id The ID of the user associated with this teacher
 * @property bool $is_active Indicates whether the teacher account is active
 */
class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'is_active',
    ];

    /**
     * Get the user associated with the teacher.
     *
     * This defines a relationship indicating that each teacher belongs
     * to a user. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses taught by the teacher.
     *
     * This defines a relationship indicating that each teacher can teach
     * multiple courses. This is a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
