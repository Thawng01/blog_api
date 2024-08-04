<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        return response()->json([
            "users" => $users
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:225",
            'email' => "required|email|unique:users",
            "password" => "required|min:6"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password
        ]);

        Auth::login($user);
        $token = auth()->user()->createToken("blog_token")->plainTextToken;
        return response()->json([
            "user" => $user,
            "token" => $token,
            "status" => "success"
        ]);
    }

    public function login(Request $request)
    {
        $credentials = Validator::make($request->all(), [
            'email' => "required|email",
            "password" => "required"
        ]);

        if ($credentials->fails()) {
            return response()->json([
                "status" => "failed",
                "message" => $credentials->errors(),

            ], 400);
        }


        if (!Auth::attempt(["email" => $request->email, "password" => $request->password])) {
            return response()->json([
                'status' => "failed",
                "message" => "Invalid credentials."
            ], 400);
        } else {
            $user = User::where("email", $request->email)->first();
            Auth::login($user);
            $token = auth()->user()->createToken("blog_token")->plainTextToken;
            return response()->json([
                "status" => "success",
                "token" => $token,
                "user" => $user
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        response()->noContent();
    }
}