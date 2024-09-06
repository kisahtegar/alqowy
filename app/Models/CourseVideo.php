<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CourseVideo Model
 *
 * Represents a video associated with a course in the application. This model
 * handles attributes related to course videos, including the video name,
 * path, and the course it belongs to. It supports soft deletion.
 *
 * @property string $name The name of the video
 * @property string $path_video The file path or URL of the video
 * @property int $course_id The ID of the course associated with this video
 */
class CourseVideo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'path_video',
        'course_id',
    ];

    /**
     * Get the course associated with the video.
     *
     * This defines a relationship indicating that each video belongs to a
     * specific course. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
