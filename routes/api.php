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

Route::post('/products', function (Request $request) {
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

Route::delete('/products/{id}', function ($id) {
    DB::table('products')->where('id', $id)->delete();
    return response()->json(['success' => true]);
});

Route::post('/orders', function (Request $request) {
    $orderId = DB::table('orders')->insertGetId([
        'customer_name' => $request->customer_name,
        'customer_email' => $request->customer_email,
        'customer_address' => $request->customer_address,
        'customer_phone' => $request->customer_phone,
        'items' => json_encode($request->items),
        'total' => $request->total,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return response()->json(['success' => true, 'order_id' => $orderId]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
