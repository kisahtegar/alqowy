<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method retrieves all categories from the database, ordered by their ID in descending order,
     * and passes them to the 'admin.categories.index' view for display.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all categories from the database, ordered by ID in descending order
        $categories = Category::orderByDesc('id')->get();
        
        // Return the view with the categories data
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * This method returns the view for creating a new category. The view contains the form 
     * where users can input the details of the new category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Return the view for creating a new category
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * This method handles the storage of a new category. It uses a database transaction to ensure 
     * that the creation of the category is atomic. The category data is validated and saved, 
     * including the handling of the category icon upload.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        // Use a database transaction to ensure atomicity
        DB::transaction(function() use ($request) {

            // Retrieve validated data from the request
            $validated = $request->validated();

            // Check if an icon file is provided and handle the file upload
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('icons', 'public');
                $validated['icon'] = $iconPath;
            } else {
                // Set a default icon if no file is provided
                $iconPath = 'images/icon-default.png';
                $validated['icon'] = $iconPath;
            }

            // Generate a slug from the category name
            $validated['slug'] = Str::slug($validated['name']);

            // Create a new category with the validated data
            $category = Category::create($validated);
        });

        // Redirect to the index page of categories
        return redirect()->route('admin.categories.index');
    }
 
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * This method retrieves a specific category instance and returns a view 
     * for editing the category. The category instance is passed to the view 
     * to pre-populate the form fields.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        // Return the edit view with the category instance
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * This method handles updating an existing category in the database. It validates 
     * the incoming request, updates the category's attributes, and handles the file 
     * upload for the category's icon if it is provided. The operation is wrapped 
     * inside a database transaction to ensure data integrity.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        DB::transaction(function() use ($request, $category) {
            // Validate the incoming request data
            $validated = $request->validated();

            // Handle icon file upload if provided
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('icons', 'public');
                $validated['icon'] = $iconPath;
            }

            // Generate and set the slug based on the category name
            $validated['slug'] = Str::slug($validated['name']);

            // Update the category with the validated data
            $category->update($validated);
        });

        // Redirect back to the categories index
        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * This method handles the deletion of a category from the database. It wraps the 
     * deletion process in a database transaction for safety. If the category is 
     * successfully deleted, the method commits the transaction and redirects to the 
     * category listing. If an exception occurs, the transaction is rolled back, and 
     * an error message is displayed.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            // Attempt to delete the category
            $category->delete();
            
            // Commit the transaction if successful
            DB::commit();

            // Redirect back to the categories index
            return redirect()->route('admin.categories.index');
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollback();

            // Redirect back to the categories index with an error message
            return redirect()->route('admin.categories.index')->with('error', 'Terjadi kesalahan ketika menghapus data.');
        }
    }
}
