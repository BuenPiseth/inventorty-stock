<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses with statistics.
     */
    public function index()
    {
        $warehouses = Warehouse::with(['products', 'stockMovements'])
            ->withCount(['products', 'stockMovements'])
            ->get()
            ->map(function ($warehouse) {
                $warehouse->total_stock_value = $warehouse->products->sum(function ($product) {
                    return $product->quantity * $product->price;
                });
                $warehouse->low_stock_count = $warehouse->products->where('quantity', '<=', 5)->count();
                return $warehouse;
            });

        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $warehouse = Warehouse::create($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    /**
     * Display warehouse details with products and statistics.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['products.category', 'stockMovements.product']);

        // Get warehouse statistics
        $stats = [
            'total_products' => $warehouse->products->count(),
            'total_stock_value' => $warehouse->products->sum(function ($product) {
                return $product->quantity * $product->price;
            }),
            'low_stock_count' => $warehouse->products->where('quantity', '<=', 5)->count(),
            'recent_movements' => $warehouse->stockMovements()
                ->with('product')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('warehouses.show', compact('warehouse', 'stats'));
    }

    /**
     * Show the form for editing the warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses,code,' . $warehouse->id,
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the warehouse (soft delete by deactivating).
     */
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has products
        if ($warehouse->products()->count() > 0) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Cannot delete warehouse with existing products. Please move products first.');
        }

        $warehouse->update(['is_active' => false]);

        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse deactivated successfully.');
    }

    /**
     * Transfer products between warehouses.
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product has enough stock in source warehouse
        if ($product->warehouse_id != $validated['from_warehouse_id']) {
            return back()->with('error', 'Product is not in the source warehouse.');
        }

        if ($product->quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock for transfer.');
        }

        // Create stock movements for transfer
        $transferData = [
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'movement_date' => now(),
        ];

        // Stock out from source warehouse
        StockMovement::create(array_merge($transferData, [
            'warehouse_id' => $validated['from_warehouse_id'],
            'type' => 'out',
            'previous_stock' => $product->quantity,
            'new_stock' => $product->quantity - $validated['quantity'],
        ]));

        // Update product quantity and warehouse
        $product->update([
            'quantity' => $product->quantity - $validated['quantity'],
        ]);

        // Check if we need to create the product in destination warehouse
        $destinationProduct = Product::where('name', $product->name)
            ->where('warehouse_id', $validated['to_warehouse_id'])
            ->first();

        if ($destinationProduct) {
            // Stock in to destination warehouse (existing product)
            StockMovement::create(array_merge($transferData, [
                'warehouse_id' => $validated['to_warehouse_id'],
                'type' => 'in',
                'previous_stock' => $destinationProduct->quantity,
                'new_stock' => $destinationProduct->quantity + $validated['quantity'],
            ]));

            $destinationProduct->update([
                'quantity' => $destinationProduct->quantity + $validated['quantity'],
            ]);
        } else {
            // Create new product in destination warehouse
            $newProduct = $product->replicate();
            $newProduct->warehouse_id = $validated['to_warehouse_id'];
            $newProduct->quantity = $validated['quantity'];
            $newProduct->save();

            // Stock in to destination warehouse (new product)
            StockMovement::create(array_merge($transferData, [
                'product_id' => $newProduct->id,
                'warehouse_id' => $validated['to_warehouse_id'],
                'type' => 'in',
                'previous_stock' => 0,
                'new_stock' => $validated['quantity'],
            ]));
        }

        return back()->with('success', 'Product transferred successfully between warehouses.');
    }
}
