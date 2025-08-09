<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BakeryCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, delete all existing categories (disable foreign key checks)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create only the 4 required categories
        $bakeryCategories = [
            ['name' => 'coffee', 'description' => 'Coffee and coffee-related products'],
            ['name' => 'cake', 'description' => 'All types of cakes and cake products'],
            ['name' => 'bakery', 'description' => 'General bakery items and baked goods'],
            ['name' => 'Cashier', 'description' => 'Point of sale and cashier operations']
        ];

        foreach ($bakeryCategories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'] ?? null
                ]
            );
        }
    }
}
