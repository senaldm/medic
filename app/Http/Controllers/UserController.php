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
            
                return response()->json(['success'=>'There is no user has registered to the system yet.'],200);
            }
            
            $userDetailsInJSON = DataResource::collection($userDetails);
            
            return response()->json($userDetailsInJSON);
        }
         
        catch (\Throwable $th) {
        
            return response()->json(['error' => "Couldn\'t get the user details. Error was {$th->getMessage()}"], 500);
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
                    'required_if:role,manager,cashier',
                    'email',
                    'unique:users,email'],
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
        catch(\Throwable $th) {

            return response()->json(['message'=> "Oops! Failed to Register the User. Error was {$th->getMessage()}"],500);
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
                    'required_if:role,manager,cashier',
                    'email',
                    'unique:users,email'
                    
                ],
                'password' => 'required_if:role,manager,cashier',
                'age' => 'required_if:role,customer',

            ],);

        $userData=$request->all();
      
        try {
            $user = User::findOrFail($id);
            if (!$user) {
                return response()->json(['error' => 'OOpsss! Cannot find the User . User might not be in the database. Trying again might work.'], 500);
            }

            $user->update($userData);

            return response()->json(['success' => 'Greate Job!  User details has been updated successfully'], 200);
        } 
        
        catch (\Throwable $th) {
        
            return response()->json(['error' => "Sorry!! A Error occurs while user details updating. Error was {$th->getMessage()}. Try again."], 500);
        
        }
    }



    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        try {
          
            User::destroy($id);
        
            return response()->json(['success'=>'User Removed Successfully.',200]);
        } 
        
        catch (\Throwable $th) {
          
            return response()->json(['error'=>"Sorry! Unable to Remove User from . Error wa {$th->getMessage()} "]);
        }
        
    }
}
