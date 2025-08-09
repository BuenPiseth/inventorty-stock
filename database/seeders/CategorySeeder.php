<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Food & Beverages'],
            ['name' => 'Books'],
            ['name' => 'Home & Garden'],
            ['name' => 'Sports & Outdoors'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Automotive'],
            ['name' => 'Office Supplies'],
            ['name' => 'Toys & Games'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
