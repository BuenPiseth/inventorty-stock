<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BakeryProductsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Clear existing products
        Product::truncate();

        // Get the 4 new categories
        $coffee = Category::where('name', 'coffee')->first();
        $cake = Category::where('name', 'cake')->first();
        $bakery = Category::where('name', 'bakery')->first();
        $cashier = Category::where('name', 'Cashier')->first();

        // Bakery products data
        $bakeryProducts = [
            // Coffee products
            [
                'name' => 'Espresso Coffee',
                'category_id' => $coffee->id,
                'unit' => 'cups',
                'quantity' => 50,
                'status' => 'active'
            ],
            [
                'name' => 'Cappuccino',
                'category_id' => $coffee->id,
                'unit' => 'cups',
                'quantity' => 30,
                'status' => 'active'
            ],
            [
                'name' => 'Coffee Beans',
                'category_id' => $coffee->id,
                'unit' => 'kg',
                'quantity' => 10,
                'status' => 'active'
            ],

            // Cake products
            [
                'name' => 'Chocolate Birthday Cake',
                'category_id' => $cake->id,
                'unit' => 'pieces',
                'quantity' => 5,
                'status' => 'active'
            ],
            [
                'name' => 'Vanilla Wedding Cake',
                'category_id' => $cake->id,
                'unit' => 'pieces',
                'quantity' => 2,
                'status' => 'active'
            ],
            [
                'name' => 'Red Velvet Cake',
                'category_id' => $cake->id,
                'unit' => 'pieces',
                'quantity' => 8,
                'status' => 'active'
            ],
            [
                'name' => 'Cheesecake',
                'category_id' => $cake->id,
                'unit' => 'pieces',
                'quantity' => 6,
                'status' => 'active'
            ],

            // Bakery products
            [
                'name' => 'Fresh Croissants',
                'category_id' => $bakery->id,
                'unit' => 'pieces',
                'quantity' => 24,
                'status' => 'active'
            ],
            [
                'name' => 'Danish Pastries',
                'category_id' => $bakery->id,
                'unit' => 'pieces',
                'quantity' => 18,
                'status' => 'active'
            ],
            [
                'name' => 'Fresh Bread Loaves',
                'category_id' => $bakery->id,
                'unit' => 'pieces',
                'quantity' => 15,
                'status' => 'active'
            ],

            // Bread
            [
                'name' => 'Sourdough Bread',
                'category_id' => $bread->id,
                'unit' => 'loaves',
                'quantity' => 12,
                'status' => 'active'
            ],
            [
                'name' => 'Whole Wheat Bread',
                'category_id' => $bread->id,
                'unit' => 'loaves',
                'quantity' => 8,
                'status' => 'active'
            ],
            [
                'name' => 'French Baguette',
                'category_id' => $bread->id,
                'unit' => 'pieces',
                'quantity' => 20,
                'status' => 'active'
            ],

            // Cookies
            [
                'name' => 'Chocolate Chip Cookies',
                'category_id' => $cookies->id,
                'unit' => 'dozen',
                'quantity' => 15,
                'status' => 'active'
            ],
            [
                'name' => 'Sugar Cookies',
                'category_id' => $cookies->id,
                'unit' => 'dozen',
                'quantity' => 12,
                'status' => 'active'
            ],
            [
                'name' => 'Oatmeal Raisin Cookies',
                'category_id' => $cookies->id,
                'unit' => 'dozen',
                'quantity' => 10,
                'status' => 'active'
            ],

            // Muffins
            [
                'name' => 'Blueberry Muffins',
                'category_id' => $muffins->id,
                'unit' => 'pieces',
                'quantity' => 24,
                'status' => 'active'
            ],
            [
                'name' => 'Chocolate Chip Muffins',
                'category_id' => $muffins->id,
                'unit' => 'pieces',
                'quantity' => 18,
                'status' => 'active'
            ],
            [
                'name' => 'Banana Nut Muffins',
                'category_id' => $muffins->id,
                'unit' => 'pieces',
                'quantity' => 16,
                'status' => 'active'
            ],

            // Donuts
            [
                'name' => 'Glazed Donuts',
                'category_id' => $donuts->id,
                'unit' => 'pieces',
                'quantity' => 30,
                'status' => 'active'
            ],
            [
                'name' => 'Chocolate Donuts',
                'category_id' => $donuts->id,
                'unit' => 'pieces',
                'quantity' => 25,
                'status' => 'active'
            ],
            [
                'name' => 'Jelly-Filled Donuts',
                'category_id' => $donuts->id,
                'unit' => 'pieces',
                'quantity' => 20,
                'status' => 'active'
            ],

            // Cupcakes
            [
                'name' => 'Vanilla Cupcakes',
                'category_id' => $cupcakes->id,
                'unit' => 'pieces',
                'quantity' => 36,
                'status' => 'active'
            ],
            [
                'name' => 'Chocolate Cupcakes',
                'category_id' => $cupcakes->id,
                'unit' => 'pieces',
                'quantity' => 32,
                'status' => 'active'
            ],
            [
                'name' => 'Red Velvet Cupcakes',
                'category_id' => $cupcakes->id,
                'unit' => 'pieces',
                'quantity' => 24,
                'status' => 'active'
            ],

            // Pies
            [
                'name' => 'Apple Pie',
                'category_id' => $pies->id,
                'unit' => 'pieces',
                'quantity' => 6,
                'status' => 'active'
            ],
            [
                'name' => 'Pumpkin Pie',
                'category_id' => $pies->id,
                'unit' => 'pieces',
                'quantity' => 4,
                'status' => 'active'
            ],
            [
                'name' => 'Cherry Pie',
                'category_id' => $pies->id,
                'unit' => 'pieces',
                'quantity' => 5,
                'status' => 'active'
            ]
        ];

        // Create products
        foreach ($bakeryProducts as $productData) {
            // Check if product already exists by name
            $existingProduct = Product::where('name', $productData['name'])->first();
            
            if (!$existingProduct) {
                Product::create($productData);
                $this->command->info("Created bakery product: {$productData['name']}");
            } else {
                $this->command->info("Product already exists: {$productData['name']}");
            }
        }

        $this->command->info('Bakery products seeding completed!');
        $this->command->info('Total bakery products processed: ' . count($bakeryProducts));
    }
}
