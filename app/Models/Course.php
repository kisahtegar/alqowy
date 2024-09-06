<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Course Model
 * 
 * Represents a course in the application. This model contains information
 * about the course, including its name, slug, trailer path, thumbnail, and
 * relationships with teachers, categories, videos, keypoints, and students.
 * It supports soft deletion.
 * 
 * @property string $name The name of the course
 * @property string $slug The slug or URL-friendly version of the course name
 * @property string|null $about A description or summary of the course
 * @property string|null $path_trailer The file path or URL of the course trailer video
 * @property string|null $thumbnail The file path or URL of the course thumbnail image
 * @property int $teacher_id The ID of the teacher associated with this course
 * @property int $category_id The ID of the category this course belongs to
 */
class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'about',
        'path_trailer',
        'thumbnail',
        'teacher_id',
        'category_id',
    ];
    
    /**
     * Get the teacher associated with the course.
     * 
     * This defines a relationship indicating that each course belongs to
     * a specific teacher. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher() 
    {
        return $this->belongsTo(Teacher::class);
    }
    
    /**
     * Get the category associated with the course.
     * 
     * This defines a relationship indicating that each course belongs to
     * a specific category. This is a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the videos associated with the course.
     * 
     * This defines a relationship indicating that each course can have
     * multiple videos. This is a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_videos() 
    {
        return $this->hasMany(CourseVideo::class);
    }

    /**
     * Get the keypoints associated with the course.
     * 
     * This defines a relationship indicating that each course can have
     * multiple keypoints. This is a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_keypoints() 
    {
        return $this->hasMany(CourseKeypoint::class);
    }

    /**
     * Get the students enrolled in the course.
     * 
     * This defines a relationship indicating that a course can have multiple
     * students enrolled. This is a many-to-many relationship with a pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students() 
    {
        return $this->belongsToMany(User::class, 'course_students');
    }
}
