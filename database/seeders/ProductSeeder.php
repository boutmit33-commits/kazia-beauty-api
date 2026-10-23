<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Wireless Headphones',
                'description' => 'Noise-cancelling over-ear headphones',
                'price' => 79.99,
                'image' => 'https://via.placeholder.com/300',
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit, tactile switches',
                'price' => 129.99,
                'image' => 'https://via.placeholder.com/300',
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 multiport adapter',
                'price' => 34.99,
                'image' => 'https://via.placeholder.com/300',
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}