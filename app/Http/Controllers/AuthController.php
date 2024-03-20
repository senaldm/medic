<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
class AuthController extends Controller
{
    use HasApiTokens;

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        
        if (Auth::user()->role=='admin') {
        
        
        $this->validate(
            $request,
            [
                'name' => 'required',
                'role' => 'required',
                'email' => [
                    'required_if:role,manager,cashier',
                    'email',
                    'unique:users,email'
                ],
                'password' => ['required_if:role,manager,cashier'],
                'age' => 'required_if:role,customer',

            ],
            //if need can be customized the error message for each to each validation fails in above . 
        );
        $userData = $request->all();


        try {
            if ($request->has('password')) {
                $userData['password'] = Hash::make($userData['password']);
            }
          
            $user= User::create($userData);

            // $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['message' => 'User Registered Successfully.',
                                    'user details'=>$user
                                    // 'token'=>$token
                                ],201);
        } 
        
        catch (\Throwable $th) {

            return response()->json(['message' => "Oops! Failed to Register the User. Error was {$th->getMessage()}"], 500);
        }
    }
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
    }
    }



    //user login to the system
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        try {

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                $token = $request->user()->createToken('authToken')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'message' => "Welcome back {$user->name}"
                ]);
            } else {

                return response()->json(['message' => 'Login Failure'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => "Error is {$th->getMessage()}"], 500);
        }
      
    }

    public function logout(){

        // $request->user()->tokens()->delete();
        Auth::logout();

    }
}
