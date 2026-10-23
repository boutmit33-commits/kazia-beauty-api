<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $exists = DB::table('users')->where('email', $request->email)->first();
        if ($exists) {
            return response()->json(['message' => 'Email already taken'], 400);
        }

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Registered successfully']);
    }

    public function login(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Str::random(60);

        DB::table('users')->where('email', $request->email)->update([
            'remember_token' => $token,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'is_admin' => $user->is_admin]
        ]);
    }

    public function logout(Request $request)
    {
        DB::table('users')->where('remember_token', $request->token)->update([
            'remember_token' => null,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Logged out successfully']);
    }
}
