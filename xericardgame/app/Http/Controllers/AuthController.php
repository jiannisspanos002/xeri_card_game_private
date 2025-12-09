<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Player ' . rand(1000, 9999),
                'email' => $validated['email'],
                'password' => bcrypt(Str::random(10)), 
            ]);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'user_id' => $user->id,
            'api_token' => $token
        ]);
    }
}
