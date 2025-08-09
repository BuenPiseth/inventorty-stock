<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Stock levels report
     */
    public function stockLevels(Request $request)
    {
        $query = Product::with('category');

        // Filter by category if specified
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by stock level
        if ($request->filled('stock_level')) {
            switch ($request->stock_level) {
                case 'low':
                    $query->where('quantity', '<=', 5);
                    break;
                case 'out':
                    $query->where('quantity', '=', 0);
                    break;
                case 'normal':
                    $query->where('quantity', '>', 5);
                    break;
            }
        }

        $products = $query->orderBy('quantity', 'asc')->get();
        $categories = Category::orderBy('name')->get();

        return view('reports.stock-levels', compact('products', 'categories'));
    }

    /**
     * Movement summary report
     */
    public function movementSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Get movement statistics
        $stats = [
            'total_in' => StockMovement::where('type', 'in')
                ->whereBetween('movement_date', [$dateFrom, $dateTo])
                ->sum('quantity'),
            'total_out' => StockMovement::where('type', 'out')
                ->whereBetween('movement_date', [$dateFrom, $dateTo])
                ->sum('quantity'),
            'movements_count' => StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])->count(),
            'products_affected' => StockMovement::whereBetween('movement_date', [$dateFrom, $dateTo])
                ->distinct('product_id')->count(),
        ];

        // Get top products by movement
        $topProducts = StockMovement::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->get();

        // Get daily movement chart data
        $dailyMovements = StockMovement::select(
                DB::raw('DATE(movement_date) as date'),
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as stock_in'),
                DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as stock_out')
            )
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.movement-summary', compact('stats', 'topProducts', 'dailyMovements', 'dateFrom', 'dateTo'));
    }

    /**
     * Low stock alert
     */
    public function lowStockAlert()
    {
        $lowStockProducts = Product::with('category')
            ->where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc')
            ->get();

        $outOfStockProducts = Product::with('category')
            ->where('quantity', '=', 0)
            ->get();

        return view('reports.low-stock-alert', compact('lowStockProducts', 'outOfStockProducts'));
    }

    /**
     * Category performance report
     */
    public function categoryPerformance(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $categoryStats = Category::with(['products' => function($query) use ($dateFrom, $dateTo) {
                $query->with(['stockMovements' => function($movementQuery) use ($dateFrom, $dateTo) {
                    $movementQuery->whereBetween('movement_date', [$dateFrom, $dateTo]);
                }]);
            }])
            ->get()
            ->map(function($category) {
                $totalProducts = $category->products->count();
                $totalStock = $category->products->sum('quantity');
                $totalValue = $category->products->sum(function($product) {
                    return $product->quantity * ($product->purchase_price ?? 0);
                });

                $movements = $category->products->flatMap->stockMovements;
                $stockIn = $movements->where('type', 'in')->sum('quantity');
                $stockOut = $movements->where('type', 'out')->sum('quantity');

                return [
                    'category' => $category,
                    'total_products' => $totalProducts,
                    'total_stock' => $totalStock,
                    'total_value' => $totalValue,
                    'stock_in' => $stockIn,
                    'stock_out' => $stockOut,
                    'net_movement' => $stockIn - $stockOut,
                ];
            })
            ->sortByDesc('total_value');

        return view('reports.category-performance', compact('categoryStats', 'dateFrom', 'dateTo'));
    }

    /**
     * Stock value report with total prices for stock in/out and remaining
     */
    public function stockValue(Request $request)
    {
        // Get date range from request or default to last 30 days
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $categoryId = $request->get('category');
        $reasonFilter = $request->get('reason');
        $movementType = $request->get('movement_type'); // all, in, out

        // Get products that have stock movements matching the filter criteria
        $productsQuery = Product::with('category')
            ->where('status', 'active')
            ->withMovementsInDateRange(
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59',
                $reasonFilter,
                $movementType
            );

        // Apply category filter if specified
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->get()->map(function ($product) use ($dateFrom, $dateTo, $reasonFilter, $movementType) {
            // Initialize stock values
            $stockIn = 0;
            $stockOut = 0;

            // Build base queries for stock movements based on movement type filter
            if (!$movementType || $movementType === 'all' || $movementType === 'in') {
                $stockInQuery = StockMovement::where('product_id', $product->id)
                    ->where('type', 'in')
                    ->whereBetween('movement_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

                // Apply reason filter if specified
                if ($reasonFilter) {
                    $stockInQuery->where('reason', $reasonFilter);
                }

                $stockIn = $stockInQuery->sum('quantity');
            }

            if (!$movementType || $movementType === 'all' || $movementType === 'out') {
                $stockOutQuery = StockMovement::where('product_id', $product->id)
                    ->where('type', 'out')
                    ->whereBetween('movement_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

                // Apply reason filter if specified
                if ($reasonFilter) {
                    $stockOutQuery->where('reason', $reasonFilter);
                }

                $stockOut = $stockOutQuery->sum('quantity');
            }

            // Get reason breakdown for this product
            $reasonBreakdown = StockMovement::where('product_id', $product->id)
                ->whereBetween('movement_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($reasonFilter, function($query) use ($reasonFilter) {
                    return $query->where('reason', $reasonFilter);
                })
                ->selectRaw('reason, type, SUM(quantity) as total_quantity')
                ->groupBy('reason', 'type')
                ->get()
                ->groupBy('reason');

            $unitPrice = $product->selling_price ?? $product->price ?? 0;

            // Get primary reason for display (most frequent reason for this product)
            $primaryReasonQuery = StockMovement::where('product_id', $product->id)
                ->whereBetween('movement_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($reasonFilter, function($query) use ($reasonFilter) {
                    return $query->where('reason', $reasonFilter);
                })
                ->when($movementType && $movementType !== 'all', function($query) use ($movementType) {
                    return $query->where('type', $movementType);
                })
                ->selectRaw('reason, COUNT(*) as count')
                ->whereNotNull('reason')
                ->groupBy('reason')
                ->orderBy('count', 'desc');

            $primaryReason = $primaryReasonQuery->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category->name ?? 'No Category',
                'unit_price' => $unitPrice,
                'current_stock' => $product->quantity,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'stock_in_value' => $stockIn * $unitPrice,
                'stock_out_value' => $stockOut * $unitPrice,
                'remaining_value' => $product->quantity * $unitPrice,
                'image_url' => $product->image_url,
                'status' => $product->status,
                'unit' => $product->unit,
                'primary_reason' => $primaryReason ? $primaryReason->reason : 'N/A',
                'reason_breakdown' => $reasonBreakdown,
                'has_movements' => ($stockIn > 0 || $stockOut > 0),
            ];
        })->filter(function ($product) {
            // Only include products that have actual movements
            return $product['has_movements'];
        });

        // Calculate totals
        $totals = [
            'total_stock_in' => $products->sum('stock_in'),
            'total_stock_out' => $products->sum('stock_out'),
            'total_remaining' => $products->sum('current_stock'),
            'total_stock_in_value' => $products->sum('stock_in_value'),
            'total_stock_out_value' => $products->sum('stock_out_value'),
            'total_remaining_value' => $products->sum('remaining_value'),
        ];

        // Get categories for filter
        $categories = Category::orderBy('name')->get();

        // Get available reasons for filter
        $availableReasons = StockMovement::getAvailableReasons();

        // Get available movement types for filter
        $movementTypes = StockMovement::getMovementTypes();

        return view('reports.stock-value', compact(
            'products',
            'totals',
            'categories',
            'dateFrom',
            'dateTo',
            'categoryId',
            'reasonFilter',
            'movementType',
            'availableReasons',
            'movementTypes'
        ));
    }



}
