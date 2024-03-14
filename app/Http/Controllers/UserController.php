<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request){
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            return response()->json(['Login Successfully']);
        }
        else {
            return response()->json(['Login Failure'],401);
        }

    }
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

   
    public function view()
    {
        try {
            $userDetails = User::all();
            $userDetailsInJSON = UserResource::collection($userDetails);
            return response()->json($userDetailsInJSON);
        } catch (\Throwable ) {
            return response()->json(['error'=>'Couldn\'t get the user data'],500);
        }
       
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            User::destroy($id);
            return response()->json(['message'=>'User Removed Successfully.',200]);
        } catch (\Throwable ) {
            return response()->json(['error'=>'Couldn\'t to Remove User ']);
        }
        
    }
}
