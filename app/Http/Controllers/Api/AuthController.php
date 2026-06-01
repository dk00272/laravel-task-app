<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
     public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);

        $token = $user->createToken('task-tracker')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (! \Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = \Auth::user()
            ->createToken('task-tracker')
            ->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => \Auth::user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}
