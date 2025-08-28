<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('items.product')->where('user_id', auth()->id())->latest()->get();
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();

        $cart = $request->input('cart'); 
        if (!$cart || count($cart) == 0) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending'
            ]);

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Not enough stock for {$product->name}"
                    ], 400);
                }

                // Calculate subtotal
                $lineTotal = $product->price * $item['quantity'];
                $total += $lineTotal;

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            // Update order total
            $order->update(['total_amount' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('items.product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Checkout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product.category')->where('id', $id)->firstOrFail();
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $order = Order::firstOrFail($id);
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
        }
        $order->delete();
        return response()->json(['message' => 'Order cancelled successfully']);
    }

    public function adminIndex()
    {
        $orders = Order::with('items.product', 'user')->get();
        return response()->json($orders);
    }

    public function cancel($id)
{
    $order = Order::where('user_id', auth()->id())->findOrFail($id);

    if ($order->status === 'delivered') {
        return response()->json(['error' => 'Delivered orders cannot be cancelled'], 400);
    }

    $order->status = 'cancelled';
    $order->save();

    return response()->json([
        'message' => 'Order cancelled successfully',
        'order' => $order
    ]);
}

}
