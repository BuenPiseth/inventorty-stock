<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $categories = Category::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('products')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $category = Category::create($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category "' . $category->name . '" has been created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the category. Please try again.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load(['products' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $category->update($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category "' . $category->name . '" has been updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the category. Please try again.');
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', 'Cannot delete category "' . $category->name . '" because it has products assigned to it.');
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category "' . $categoryName . '" has been deleted successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'An error occurred while deleting the category. Please try again.');
        }
    }
}
