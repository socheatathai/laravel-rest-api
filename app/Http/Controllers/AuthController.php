<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(LoginRequest $request)
    {
        $response    = ['error' => true, 'message' => 'Invalid credentials'];
        $status_code = 400;
        $user        = $request->user;
        $password    = $request->password;
        
        if(Hash::check($password, $user->password)){
            $response = [
                'name'   => $user->first_name . $user->last_name,
                'source' => $user->source,
                'token'  => str()::random(200)
            ];
            $status_code = 200;
        }
        return response()->json($response, $status_code);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete(); // Revoke all tokens for the user

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }

    
}
