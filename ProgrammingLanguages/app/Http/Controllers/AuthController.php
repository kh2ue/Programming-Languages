<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth:sanctum', ['only' => 'logout']);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        
        $user = User::create($data);
        
        return response()->json([
            'data' => $data,
            'message'=> 'Registerd successfully'
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('phone_number', $data['phone_number'])->first();

        if (!is_null($user)) {

            if (!Auth::attempt(['phone_number' => $data['phone_number'], 'password' => $data['password']])) {
                $message = "User phone_number or password doesn't exist";

                return response()->json([
                    "message" => $message
                ], 401);
            } 

            else {
                $user['access_token'] = $user->createToken("token")->plainTextToken;
                
                return response()->json([
                        'data' => $user,
                        'message' => 'User Logged in Successfully'
                    ]);
            }
        } 
        
        else {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response () -> json ([
            'message' => 'Successfully logged out'
        ], 200);
    }
}
