<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $userDetails = User::all();

            if($userDetails->isEmpty()){
            
                return response()->json(['message'=>'There is no user has registered to the system yet.'],200);
            }
            
            $userDetailsInJSON = DataResource::collection($userDetails);
            
            return response()->json($userDetailsInJSON);
        }
         
        catch (\Throwable) {
        
            return response()->json(['error' => 'Couldn\'t get the user data'], 500);
        }
    }

  

    //user login to the system
    public function login(Request $request){

        $credentials = $request->only('email','password');
        
        if(Auth::attempt($credentials)){
        
            return response()->json(['Login Successfully']);
        }
        else {
        
            return response()->json(['Login Failure'],401);
        }

    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        

        $this->validate($request,[
            'name'=>'required',
            'role' => 'required',
            'email'=>[
                'email',
                'unique:users,email',
                'required_if:role,manager,cashier'],
            'password'=>'required_if:role,manager,cashier',
            'age'=>'required_if:role,customer',
        
        ],
        //if need can be customized the error message for each to each validation fails in above . 
        );
        $userData = $request->all();
        

        try {
            $userData['password'] = Hash::make($userData['password']);
            User::create($userData);
        
            return response()->json(['message'=>'User Registered Successfully.',201]);

        }
        catch(\Throwable ) {

            return response()->json(['message'=>'Oops! Failed to Register the User.Try Again '],500);
        }


    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'required',
                'role' => 'required',
                'email' => [
                    'email',
                    'unique:users,email',
                    'required_if:role,manager,cashier'
                ],
                'password' => 'required_if:role,manager,cashier',
                'age' => 'required_if:role,customer',

            ],);

        $userData=$request->all();
        try {
        $user=User::find($id);
       
            $user->name=$userData['name'];
            $user->email=$userData['email'];
            $user->role=$userData['role'];
            $user->passoword=$userData['password'];
            $user->age=$userData['age'];
            $user->save();

            return response()->json(['message'=>'User Updated Successfully.'],200);
        }
        catch(\Throwable ){
            return response()->json(['error'=>'Oops! Couldn\'t Update the User Details'],500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        try {
          
            User::destroy($id);
        
            return response()->json(['message'=>'User Removed Successfully.',200]);
        } 
        
        catch (\Throwable ) {
          
            return response()->json(['error'=>'Couldn\'t to Remove User ']);
        }
        
    }
}
