<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Product Controller
 *
 * This controller handles all CRUD operations for products.
 * It includes proper eager loading, validation, and error handling.
 */
class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * This method shows all products with pagination and search functionality.
     * It uses eager loading to prevent N+1 query problems.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // Get filters from request
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status');
        $lowStock = $request->get('low_stock');

        // Build query with eager loading for category relationship
        $query = Product::with('category');

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($category) {
            $query->where('category_id', $category);
        }

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter low stock items
        if ($lowStock) {
            $query->where('quantity', '<=', 5);
        }

        // Get paginated results (15 per page) ordered by creation date
        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Append search query to pagination links to maintain search state
        $products->appends($request->query());

        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('products.modern-index', compact('products', 'search', 'categories', 'category', 'status', 'lowStock'));
    }

    /**
     * Show the form for creating a new product.
     *
     * This method displays the product creation form with all necessary data.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        // Get all categories for the dropdown, ordered by name
        $categories = Category::orderBy('name')->get();

        return view('products.modern-create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * This method handles the creation of new products with proper validation.
     *
     * @param \App\Http\Requests\StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            Log::info('Product creation started', ['request_data' => $request->all()]);

            $data = $request->validated();
            Log::info('Validation passed', ['validated_data' => $data]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
                Log::info('Image uploaded', ['image_path' => $imagePath]);
            }

            // Create new product with validated data
            $product = Product::create($data);
            Log::info('Product created successfully', ['product_id' => $product->id, 'product_name' => $product->name]);

            // Redirect to products index with success message
            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $product->name . '" has been created successfully!');

        } catch (\Exception $e) {
            // Handle any unexpected errors
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the product. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     *
     * This method shows detailed information about a specific product.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product): View
    {
        // Load the category relationship for display
        $product->load('category');

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * This method displays the product edit form with current data.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product): View
    {
        // Get all categories for the dropdown, ordered by name
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * This method handles updating existing products with proper validation.
     *
     * @param \App\Http\Requests\UpdateProductRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            Log::info('Product update started', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'request_data' => $request->all()
            ]);

            $data = $request->validated();
            Log::info('Update validation passed', ['validated_data' => $data]);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
                Log::info('Image updated', ['image_path' => $imagePath]);
            }

            // Update product with validated data
            $product->update($data);
            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'updated_name' => $product->fresh()->name
            ]);

            // Redirect to products index with success message
            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $product->name . '" has been updated successfully!');

        } catch (\Exception $e) {
            // Handle any unexpected errors
            Log::error('Product update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the product. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * This method handles the deletion of products with proper error handling.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            // Store product name for the success message
            $productName = $product->name;

            // Delete the product
            $product->delete();

            // Redirect with success message
            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $productName . '" has been deleted successfully!');

        } catch (\Exception $e) {
            // Handle any errors (e.g., foreign key constraints)
            return redirect()
                ->route('products.index')
                ->with('error', 'Unable to delete the product. It may be referenced by other records.');
        }
    }
}
