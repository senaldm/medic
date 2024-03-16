<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'manager') {
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
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
    }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'manager') {
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

            $user->update($userData);

            return response()->json(['success' => 'Greate Job!  User details has been updated successfully'], 200);
        } 
        
        catch (\Throwable $th) {
        
            return response()->json(['error' => "Sorry!! A Error occurs while user details updating. Error was {$th->getMessage()}. Try again."], 500);
        
        }
    }
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
    }
    }



    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'manager') {
        try {
          
            User::destroy($id);
        
            return response()->json(['success'=>'User Removed Successfully.',200]);
        } 
        
        catch (\Throwable $th) {
          
            return response()->json(['error'=>"Sorry! Unable to Remove User from . Error wa {$th->getMessage()} "]);
        }
    }
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
    }
    }



    public function checkCustomer($id){
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'cashier'|| Auth::user()->role == 'mananger') {
        try {
            
            User::findOrFail($id);

            return response()->json(['success'=>'Customer is found'],200);
        } 
        
        catch (\Throwable $th) {
            return response()->json(['Error'=>$th->getMessage()],500);
        }
    }
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
    }
    }
}
