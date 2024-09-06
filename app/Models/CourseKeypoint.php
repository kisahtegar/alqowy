<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CourseKeypoint Model
 *
 * Represents a keypoint or highlight of a course. This model stores key
 * details about a course that can be used to summarize or highlight important
 * aspects of the course. It supports soft deletion.
 *
 * @property string $name The name or title of the keypoint
 * @property int $course_id The ID of the course associated with this keypoint
 */
class CourseKeypoint extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'course_id',
    ];

    /**
     * Get the course associated with the keypoint.
     *
     * This defines a relationship indicating that each keypoint belongs
     * to a specific course. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
