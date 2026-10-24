<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', function (Request $request) {
    $query = DB::table('products');
    if ($request->has('category') && $request->category !== 'All') {
        $query->where('category', $request->category);
    }
    if ($request->has('search') && $request->search !== '') {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    $products = $query->get();
    return response()->json($products);
});

Route::get('/products/{id}', function ($id) {
    $product = DB::table('products')->where('id', $id)->first();
    return response()->json($product);
});

// Only logged-in users can reach these, and each one checks for admin
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/products', function (Request $request) {
        abort_if(! $request->user()->is_admin, 403);
        $id = DB::table('products')->insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image ?? 'https://via.placeholder.com/300',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'id' => $id]);
    });

    Route::put('/products/{id}', function (Request $request, $id) {
        abort_if(! $request->user()->is_admin, 403);
        DB::table('products')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image ?? 'https://via.placeholder.com/300',
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    });

    Route::delete('/products/{id}', function (Request $request, $id) {
        abort_if(! $request->user()->is_admin, 403);
        DB::table('products')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    });
});

Route::post('/orders', function (Request $request) {
    // 1. Check the data is valid (the server-side validation)
    $data = $request->validate([
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_address' => 'required|string|max:500',
        'customer_phone' => 'required|string|max:30',
        'items' => 'required|array|min:1',
        'items.*.id' => 'required|integer',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    // 2. Do everything as one block: if anything fails, nothing is saved
    return DB::transaction(function () use ($data) {
        $subtotal = 0;
        $orderItems = [];

        foreach ($data['items'] as $item) {
            $product = DB::table('products')->where('id', $item['id'])->lockForUpdate()->first();

            if (! $product) {
                abort(422, 'A product in your cart no longer exists.');
            }
            if ($product->stock < $item['quantity']) {
                abort(422, "Not enough stock for {$product->name}.");
            }

            // 3. Update the stock
            DB::table('products')->where('id', $product->id)->decrement('stock', $item['quantity']);

            $subtotal += $product->price * $item['quantity'];
            $orderItems[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
            ];
        }

        // 4. The server calculates the total itself, not the browser
        $shipping = $subtotal >= 50 ? 0 : 5.99;

        $orderId = DB::table('orders')->insertGetId([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_address' => $data['customer_address'],
            'customer_phone' => $data['customer_phone'],
            'items' => json_encode($orderItems),
            'total' => $subtotal + $shipping,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'order_id' => $orderId]);
    });
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);