<?php

namespace App\Http\Controllers;

use App\Models\CustomerDrugDetails;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
class CustomerDrugDetailsController extends Controller
{
    public function index(){
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
}
