<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Stock Movement Controller
 *
 * Handles stock in/out operations and movement tracking
 */
class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        // Filter by product if specified
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type if specified
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range if specified
        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->orderBy('movement_date', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    /**
     * Show the form for creating a new stock movement.
     */
    public function create(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $type = $request->get('type', 'in'); // Default to stock in

        return view('stock-movements.create', compact('products', 'type'));
    }

    /**
     * Store a newly created stock movement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'reason' => 'nullable|in:St360,Bakery,Cake,Koh Pich,Cashier',
            'movement_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $previousStock = $product->quantity;

            // Calculate new stock based on movement type
            if ($request->type === 'in') {
                $newStock = $previousStock + $request->quantity;
            } else {
                // Stock out - check if sufficient stock available
                if ($previousStock < $request->quantity) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Insufficient stock. Available: ' . $previousStock);
                }
                $newStock = $previousStock - $request->quantity;
            }

            // Create stock movement record
            StockMovement::create([
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $request->unit_cost,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'reason' => $request->reason,
                'user_id' => Auth::id(),
                'movement_date' => $request->movement_date,
            ]);

            // Update product quantity
            $product->update(['quantity' => $newStock]);

            DB::commit();

            $message = $request->type === 'in'
                ? 'Stock added successfully!'
                : 'Stock removed successfully!';

            return redirect()->route('stock-movements.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing the stock movement.');
        }
    }

    /**
     * Display the specified stock movement.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product.category', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show stock in form.
     */
    public function stockIn()
    {
        $products = Product::orderBy('name')->get();
        return view('stock-movements.stock-in', compact('products'));
    }

    /**
     * Show stock out form.
     */
    public function stockOut()
    {
        $products = Product::where('quantity', '>', 0)->orderBy('name')->get();
        return view('stock-movements.stock-out', compact('products'));
    }

    /**
     * Get product details for AJAX requests.
     */
    public function getProductDetails(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'current_stock' => $product->quantity,
            'unit' => $product->unit,
            'min_stock' => $product->min_stock,
            'status' => $product->status,
        ]);
    }

    /**
     * Show the form for editing the specified stock movement.
     */
    public function edit(StockMovement $stockMovement)
    {
        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('stock-movements.edit', compact('stockMovement', 'products'));
    }

    /**
     * Update the specified stock movement in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:St360,Bakery,Cake,Koh Pich,Cashier',
            'notes' => 'nullable|string|max:500',
            'movement_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $oldProduct = Product::findOrFail($stockMovement->product_id);

            // Reverse the old stock movement
            if ($stockMovement->type === 'in') {
                $oldProduct->quantity -= $stockMovement->quantity;
            } else {
                $oldProduct->quantity += $stockMovement->quantity;
            }
            $oldProduct->save();

            // Calculate new stock after the updated movement
            $newStock = $request->type === 'in'
                ? $product->quantity + $request->quantity
                : $product->quantity - $request->quantity;

            // Validate stock won't go negative
            if ($newStock < 0) {
                throw new \Exception('Insufficient stock. Available: ' . $product->quantity);
            }

            // Update the stock movement
            $stockMovement->update([
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'previous_stock' => $product->quantity,
                'new_stock' => $newStock,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'movement_date' => $request->movement_date,
                'user_id' => Auth::id(),
            ]);

            // Update product quantity
            $product->update(['quantity' => $newStock]);

            DB::commit();

            return redirect()->route('stock-movements.index')
                ->with('success', 'Stock movement updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified stock movement from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($stockMovement->product_id);

            // Reverse the stock movement
            if ($stockMovement->type === 'in') {
                $newStock = $product->quantity - $stockMovement->quantity;
            } else {
                $newStock = $product->quantity + $stockMovement->quantity;
            }

            // Check if stock would go negative
            if ($newStock < 0) {
                DB::rollback();

                // Provide detailed information and options
                $shortfall = abs($newStock);
                $movementType = $stockMovement->type === 'in' ? 'Stock In' : 'Stock Out';
                $productName = $product->name;
                $currentStock = $product->quantity;
                $movementQuantity = $stockMovement->quantity;

                $errorMessage = "Cannot delete this {$movementType} movement for '{$productName}'. " .
                               "Current stock: {$currentStock}, Movement quantity: {$movementQuantity}. " .
                               "Deleting would result in negative stock (-{$shortfall}). " .
                               "Please adjust stock levels first or use Force Delete option.";

                return back()->with('error', $errorMessage)
                           ->with('show_force_delete', true)
                           ->with('movement_id', $stockMovement->id)
                           ->with('product_id', $product->id)
                           ->with('product_name', $productName)
                           ->with('shortfall', $shortfall);
            }

            // Update product quantity
            $product->update(['quantity' => $newStock]);

            // Soft delete the stock movement (moves to Recycle Bin)
            $stockMovement->delete();

            DB::commit();

            // Preserve filters from request if present
            $filters = request()->only(['product_id','type','date_from','date_to']);

            return redirect()->route('stock-movements.index', $filters)
                ->with('success', 'Stock movement moved to Recycle Bin successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Force delete a stock movement (allows negative stock)
     */
    public function forceDestroy(StockMovement $stockMovement)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($stockMovement->product_id);

            // Reverse the stock movement (allow negative stock)
            if ($stockMovement->type === 'in') {
                $newStock = $product->quantity - $stockMovement->quantity;
            } else {
                $newStock = $product->quantity + $stockMovement->quantity;
            }

            // Update product quantity (even if negative)
            $product->update(['quantity' => $newStock]);

            // Soft delete the stock movement
            $stockMovement->delete();

            DB::commit();

            $warningMessage = "Stock movement deleted successfully! ";
            if ($newStock < 0) {
                $warningMessage .= "WARNING: Product '{$product->name}' now has negative stock ({$newStock}). Please adjust stock levels.";
            }

            // Preserve filters from request if present
            $filters = request()->only(['product_id','type','date_from','date_to']);

            return redirect()->route('stock-movements.index', $filters)
                ->with('warning', $warningMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the Recycle Bin with soft-deleted stock movements
     */
    public function recycleBin(Request $request)
    {
        $query = StockMovement::onlyTrashed()->with(['product', 'user']);

        // Apply filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $trashedMovements = $query->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('stock-movements.recycle-bin', compact('trashedMovements', 'products'));
    }

    /**
     * Restore a soft-deleted stock movement
     */
    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $movement = StockMovement::onlyTrashed()->findOrFail($id);
            $product = Product::findOrFail($movement->product_id);

            // Re-apply the original stock effect when restoring
            if ($movement->type === 'in') {
                $newStock = $product->quantity + $movement->quantity;
            } else {
                // For stock out, ensure we have enough stock to subtract
                if ($product->quantity < $movement->quantity) {
                    throw new \Exception("Cannot restore: Insufficient stock. Current stock: {$product->quantity}, Required: {$movement->quantity}");
                }
                $newStock = $product->quantity - $movement->quantity;
            }

            // Update product quantity
            $product->update(['quantity' => $newStock]);

            // Restore the movement
            $movement->restore();

            DB::commit();

            return redirect()->route('stock-movements.recycle-bin')
                ->with('success', 'Stock movement restored successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Permanently delete a stock movement from the recycle bin
     */
    public function forceDelete($id)
    {
        try {
            $movement = StockMovement::onlyTrashed()->findOrFail($id);
            $productName = $movement->product->name ?? 'Unknown Product';

            $movement->forceDelete();

            return redirect()->route('stock-movements.recycle-bin')
                ->with('success', "Stock movement for '{$productName}' permanently deleted!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Bulk restore multiple stock movements
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'movement_ids' => 'required|array',
            'movement_ids.*' => 'exists:stock_movements,id'
        ]);

        DB::beginTransaction();

        try {
            $restoredCount = 0;
            $errors = [];

            foreach ($request->movement_ids as $id) {
                try {
                    $movement = StockMovement::onlyTrashed()->findOrFail($id);
                    $product = Product::findOrFail($movement->product_id);

                    // Re-apply the original stock effect
                    if ($movement->type === 'in') {
                        $newStock = $product->quantity + $movement->quantity;
                    } else {
                        if ($product->quantity < $movement->quantity) {
                            $errors[] = "Cannot restore movement for {$movement->product->name}: Insufficient stock";
                            continue;
                        }
                        $newStock = $product->quantity - $movement->quantity;
                    }

                    $product->update(['quantity' => $newStock]);
                    $movement->restore();
                    $restoredCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error restoring movement ID {$id}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully restored {$restoredCount} movement(s)";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return redirect()->route('stock-movements.recycle-bin')
                ->with($restoredCount > 0 ? 'success' : 'error', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Bulk permanently delete multiple stock movements
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'movement_ids' => 'required|array',
            'movement_ids.*' => 'exists:stock_movements,id'
        ]);

        try {
            $movements = StockMovement::onlyTrashed()->whereIn('id', $request->movement_ids);
            $count = $movements->count();
            $movements->forceDelete();

            return redirect()->route('stock-movements.recycle-bin')
                ->with('success', "Successfully permanently deleted {$count} movement(s)!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Quick stock adjustment to fix negative stock
     */
    public function quickAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $previousStock = $product->quantity;
            $newStock = $previousStock + $request->adjustment_quantity;

            // Create stock movement record
            StockMovement::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $product->warehouse_id ?? 1,
                'type' => 'in',
                'quantity' => $request->adjustment_quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => 0,
                'reference' => 'STOCK_ADJUSTMENT',
                'notes' => 'Quick adjustment: ' . $request->reason,
                'reason' => 'St360',
                'user_id' => Auth::id(),
                'movement_date' => now(),
            ]);

            // Update product quantity
            $product->update(['quantity' => $newStock]);

            DB::commit();

            return redirect()->route('stock-movements.index')
                ->with('success', "Stock adjusted successfully! Product '{$product->name}' stock increased from {$previousStock} to {$newStock}.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
