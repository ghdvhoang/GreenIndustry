<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // email checking 
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            if (isset($user) && $user->count() > 0) {
                return response([
                    'message' => 'Invalid credentials!'
                ], 401);
            } else {
                return response([
                    'message' => 'User not found!'
                ], 401);
            }
        } else if ($user->user_role == 'general') {

            // $user->tokens()->delete();

            $token = $user->createToken('auth-token')->plainTextToken;

            // $user->photo = get_photo('user_image', $user->photo);

            $response = [
                'message' => 'Login successful',
                'user' => $user,
                'user_id' => $user->id,
                'user_image' => get_user_images($user->id),
                'cover_photo' => get_cover_photos($user->id),
                'token' => $token
            ];

            return response($response, 201);

        } else {

            //user not authorized
            return response()->json([
                'message' => 'User not found!',
            ], 400);
        }

    }
}
