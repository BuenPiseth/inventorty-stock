<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventoryDataSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get or create categories
        $ingredients = Category::firstOrCreate(['name' => 'Ingredients']);
        $specialtyItems = Category::firstOrCreate(['name' => 'Specialty Items']);
        $cookies = Category::firstOrCreate(['name' => 'Cookies']);

        // Inventory data from the spreadsheet
        $inventoryData = [
            [
                'name' => 'TWIN PACK Organic',
                'category_id' => $ingredients->id,
                'unit' => 'pack',
                'quantity' => 110,
                'status' => 'active'
            ],
            [
                'name' => 'MASTER SAVE',
                'category_id' => $ingredients->id,
                'unit' => 'pack',
                'quantity' => 13,
                'status' => 'active'
            ],
            [
                'name' => 'COCONUT JELLY',
                'category_id' => $specialtyItems->id,
                'unit' => 'pack',
                'quantity' => 18,
                'status' => 'active'
            ],
            [
                'name' => 'TWIN PACK Brown Sugar',
                'category_id' => $ingredients->id,
                'unit' => 'pack',
                'quantity' => 32,
                'status' => 'active'
            ],
            [
                'name' => 'Syrup Brown Sugar',
                'category_id' => $ingredients->id,
                'unit' => 'bottle',
                'quantity' => 5,
                'status' => 'active'
            ],
            [
                'name' => 'VANILLA WAFERS',
                'category_id' => $cookies->id,
                'unit' => 'box',
                'quantity' => 3,
                'status' => 'active'
            ]
        ];

        // Create products
        foreach ($inventoryData as $productData) {
            // Check if product already exists by name
            $existingProduct = Product::where('name', $productData['name'])->first();

            if (!$existingProduct) {
                Product::create($productData);
                $this->command->info("Created product: {$productData['name']}");
            } else {
                // Update existing product with new data
                $existingProduct->update($productData);
                $this->command->info("Updated product: {$productData['name']}");
            }
        }

        $this->command->info('Inventory data seeding completed!');
        $this->command->info('Total products processed: ' . count($inventoryData));
    }
}
