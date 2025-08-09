<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    /**
     * Show the import form
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Handle CSV import
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        $importResults = [
            'success' => 0,
            'errors' => 0,
            'updated' => 0,
            'messages' => []
        ];

        foreach ($csvData as $row) {
            if (count($row) < count($header)) {
                continue; // Skip incomplete rows
            }

            $data = array_combine($header, $row);
            
            try {
                // Validate required fields
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'category' => 'required|string',
                    'unit' => 'required|string',
                    'quantity' => 'required|integer|min:0',
                    'status' => 'sometimes|in:active,inactive'
                ]);

                if ($validator->fails()) {
                    $importResults['errors']++;
                    $importResults['messages'][] = "Row skipped - {$data['name']}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Find or create category
                $category = Category::firstOrCreate(['name' => $data['category']]);

                // Prepare product data
                $productData = [
                    'name' => $data['name'],
                    'category_id' => $category->id,
                    'unit' => $data['unit'],
                    'quantity' => (int)$data['quantity'],
                    'status' => $data['status'] ?? 'active'
                ];

                // Check if product exists
                $existingProduct = Product::where('name', $data['name'])->first();

                if ($existingProduct) {
                    $existingProduct->update($productData);
                    $importResults['updated']++;
                    $importResults['messages'][] = "Updated: {$data['name']}";
                } else {
                    Product::create($productData);
                    $importResults['success']++;
                    $importResults['messages'][] = "Created: {$data['name']}";
                }

            } catch (\Exception $e) {
                $importResults['errors']++;
                $importResults['messages'][] = "Error processing {$data['name']}: " . $e->getMessage();
            }
        }

        return redirect()
            ->route('import.index')
            ->with('import_results', $importResults);
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_template.csv"',
        ];

        $csvData = [
            ['name', 'category', 'unit', 'quantity', 'status'],
            ['Chocolate Cake', 'Cakes', 'pieces', '5', 'active'],
            ['Fresh Croissants', 'Pastries', 'pieces', '24', 'active'],
            ['Sourdough Bread', 'Bread', 'loaves', '12', 'active'],
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
