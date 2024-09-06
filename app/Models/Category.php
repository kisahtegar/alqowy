<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Category Model
 * 
 * Represents a category for courses in the application. Categories
 * can have multiple associated courses. The model uses soft deletion
 * and mass-assignment for certain attributes.
 * 
 * @property string $name The name of the category
 * @property string $slug The slug of the category, used in URLs
 * @property string|null $icon The optional icon representing the category
 */
class Category extends Model
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
        'icon',
    ];

    /**
     * Get the courses that belong to this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses() {
        return $this->hasMany(Course::class);
    }
}
