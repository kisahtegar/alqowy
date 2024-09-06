<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CourseStudent Model
 * 
 * Represents the association between a user (student) and a course. This model
 * tracks which users are enrolled in which courses. It supports soft deletion.
 * 
 * @property int $user_id The ID of the user (student) enrolled in the course
 * @property int $course_id The ID of the course the user is enrolled in
 */
class CourseStudent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
    ];
}
