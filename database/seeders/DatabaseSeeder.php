<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
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

        // User::create([
        //     'name' => 'Test User',
        //     'email' => 'testingRole@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'admin', 
        // ]);
        // Category::create([
        //     'name' => 'Electronics',
        // ]);

        // Product::create([
        //     'name' => 'Smartphone',
        //     'description' => 'Latest model smartphone with advanced features.',
        //     'price' => 699.99,
        //     'stock' => 50,
        //     'image' => 'smartphone.jpg',
        //     'category_id' => 1, // Assuming the first category is Electronics
        // ]);

        Order::create([
            'user_id' => 2, // Assuming the first user is Test User
            'total_amount' => 699.99,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => 2, // Assuming the first order
            'product_id' => 3, // Assuming the first product is Smartphone
            'quantity' => 1,
            'price' => 699.99,
        ]);
        OrderItem::create([
            'order_id' => 2, // Assuming the first order
            'product_id' => 5, // Assuming the first product is Smartphone
            'quantity' => 2,
            'price' => 699.99,
        ]);
    }
}
