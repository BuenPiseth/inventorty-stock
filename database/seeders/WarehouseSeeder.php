<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'St360 Warehouse',
                'code' => 'ST360',
                'description' => 'Main warehouse located at Street 360',
                'address' => 'Street 360, Phnom Penh, Cambodia',
                'phone' => '+855 12 345 678',
                'email' => 'st360@deerbakery.com',
                'manager_name' => 'Manager St360',
                'is_active' => true,
            ],
            [
                'name' => 'Koh Pich Warehouse',
                'code' => 'KOHPICH',
                'description' => 'Secondary warehouse located at Koh Pich',
                'address' => 'Koh Pich, Phnom Penh, Cambodia',
                'phone' => '+855 12 345 679',
                'email' => 'kohpich@deerbakery.com',
                'manager_name' => 'Manager Koh Pich',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
