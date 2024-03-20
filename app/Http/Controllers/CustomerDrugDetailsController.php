<?php

namespace App\Http\Controllers;

use App\Models\CustomerDrugDetails;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Auth;
class CustomerDrugDetailsController extends Controller
{
    public function index(){
        if(Auth::user()->role=='admin'){
        try {
            $customerPastDrugDetails = CustomerDrugDetails::all();
            if ($customerPastDrugDetails->isEmpty()) {

                return response()->json(['success' => 'There is no details has updated to the system yet.'], 200);
            }

            $customerPastDrugDetailsInJSON = DataResource::collection($customerPastDrugDetails);
            
            return response()->json($customerPastDrugDetailsInJSON);

        } 
        catch (\Throwable $th) {

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    else {
            return response()->json(['error' => "You are not permitted to this operation. Try again with authorized access."], 401);
        
    }
        

    }
}
