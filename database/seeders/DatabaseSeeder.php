<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Test User',
            'email' => 'testingRole@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin', 
        ]);
        Category::create([
            'name' => 'Electronics',
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest model smartphone with advanced features.',
            'price' => 699.99,
            'stock' => 50,
            'image' => 'smartphone.jpg',
            'category_id' => 1, // Assuming the first category is Electronics
        ]);
    }
}
