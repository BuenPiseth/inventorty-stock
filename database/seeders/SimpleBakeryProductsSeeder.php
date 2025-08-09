<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleBakeryProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products (disable foreign key checks)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Get the 4 categories
        $coffee = Category::where('name', 'coffee')->first();
        $cake = Category::where('name', 'cake')->first();
        $bakery = Category::where('name', 'bakery')->first();
        $cashier = Category::where('name', 'Cashier')->first();

        // Simple products for the 4 categories
        $products = [
            // Coffee products
            ['name' => 'Espresso Coffee', 'category_id' => $coffee->id, 'unit' => 'cups', 'quantity' => 50, 'status' => 'active'],
            ['name' => 'Cappuccino', 'category_id' => $coffee->id, 'unit' => 'cups', 'quantity' => 30, 'status' => 'active'],
            ['name' => 'Coffee Beans', 'category_id' => $coffee->id, 'unit' => 'kg', 'quantity' => 10, 'status' => 'active'],
            ['name' => 'Latte', 'category_id' => $coffee->id, 'unit' => 'cups', 'quantity' => 25, 'status' => 'active'],
            ['name' => 'Americano', 'category_id' => $coffee->id, 'unit' => 'cups', 'quantity' => 40, 'status' => 'active'],
            
            // Cake products
            ['name' => 'Chocolate Birthday Cake', 'category_id' => $cake->id, 'unit' => 'pieces', 'quantity' => 5, 'status' => 'active'],
            ['name' => 'Vanilla Wedding Cake', 'category_id' => $cake->id, 'unit' => 'pieces', 'quantity' => 2, 'status' => 'active'],
            ['name' => 'Red Velvet Cake', 'category_id' => $cake->id, 'unit' => 'pieces', 'quantity' => 8, 'status' => 'active'],
            ['name' => 'Cheesecake', 'category_id' => $cake->id, 'unit' => 'pieces', 'quantity' => 6, 'status' => 'active'],
            ['name' => 'Strawberry Cake', 'category_id' => $cake->id, 'unit' => 'pieces', 'quantity' => 4, 'status' => 'active'],

            // Bakery products
            ['name' => 'Fresh Croissants', 'category_id' => $bakery->id, 'unit' => 'pieces', 'quantity' => 24, 'status' => 'active'],
            ['name' => 'Danish Pastries', 'category_id' => $bakery->id, 'unit' => 'pieces', 'quantity' => 18, 'status' => 'active'],
            ['name' => 'Fresh Bread Loaves', 'category_id' => $bakery->id, 'unit' => 'pieces', 'quantity' => 15, 'status' => 'active'],
            ['name' => 'Blueberry Muffins', 'category_id' => $bakery->id, 'unit' => 'pieces', 'quantity' => 20, 'status' => 'active'],
            ['name' => 'Chocolate Chip Cookies', 'category_id' => $bakery->id, 'unit' => 'dozen', 'quantity' => 12, 'status' => 'active'],
            ['name' => 'Glazed Donuts', 'category_id' => $bakery->id, 'unit' => 'pieces', 'quantity' => 30, 'status' => 'active'],

            // Cashier items
            ['name' => 'Gift Cards', 'category_id' => $cashier->id, 'unit' => 'pieces', 'quantity' => 100, 'status' => 'active'],
            ['name' => 'Receipt Paper', 'category_id' => $cashier->id, 'unit' => 'rolls', 'quantity' => 50, 'status' => 'active'],
            ['name' => 'Shopping Bags', 'category_id' => $cashier->id, 'unit' => 'pieces', 'quantity' => 200, 'status' => 'active'],
            ['name' => 'Cash Register Tape', 'category_id' => $cashier->id, 'unit' => 'rolls', 'quantity' => 25, 'status' => 'active']
        ];

        // Create products
        foreach ($products as $productData) {
            Product::create($productData);
            $this->command->info("Created product: {$productData['name']}");
        }

        $this->command->info('Simple bakery products seeding completed!');
    }
}
