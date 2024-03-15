<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataResource;
use App\Models\CustomerDrugDetails;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            
            $inventory = Inventory::all();
            if($inventory->isEmpty()){

                return response()->json(['message'=>'There is no details to view for existing medicine'],200);
            }

            $inventoryDetailsInJson=DataResource::collection($inventory);

            return response()->json($inventoryDetailsInJson);
        } 
        
        catch (\Throwable) {

            return response()->json(['error' => 'Couldn\'t get the Inventory Data'], 500);
        }
    }

 

    /**
     * Store a new resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'batch_no'=>['required',
                        'unique:inventories,batch_no',],
            'name'=> 'required',
            'seller_company' => 'required',
            'buying_price' => 'required',
            'sell_price' => 'required',
            'quantity' => 'required',
            
        
            ],
        );

        $inventoryData=$request->all();

        try 
        {
            Inventory::create($inventoryData);
            
            return response()->json(['message'=>'Medicine details added to the system successfully'],200);

        } catch (\Throwable $th) {

            return response()->json(['error' => "ERRORR!! {$th->getMessage()} "], 500);
        }
    }

  
    // update the existing details of inventory

    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'batch_no' => [
                    'required',
                    'unique:inventories,batch_no',
                ],
                'name' => 'required',
                'seller_company' => 'required',
                'buying_price' => 'required',
                'sell_price' => 'required',
                'quantity' => 'required',


            ],
        );

        $inventoryData=$request->all();


        try {
            $inventoryID=Inventory::findOrFail($id);
            if(!$inventoryID){
                return response()->json(['error'=>'OOpsss! Cannot find the Batch No. It might not be in the database  now. 
                Try with valid Batch No again might work.'],500);
            }

            // here I assumed as the quantity will be updated as exactly current quantity of each item instead of adding or subtracting excisting quantity
            $inventoryID->update($inventoryData);
            
            return response()->json(['success'=>'Greate! Medicine details has been updated successfully'],200);
        }
        
        catch (\Throwable $th) {
            return response()->json(['error'=>"Sorry!! A Error occurs while medicine details updating. Error was {$th->getMessage()}. Try again."],500);
        }

    }
        
   
    /* Quantity will be check each time when cachier add the quatity*/
   

    public function  checkQuatity($id,$requestQuantity) {
        try {
            
            $existingQuantity = Inventory::where('batch_no', $id)->value('quantity');

            If($existingQuantity>$requestQuantity){
                return response()->json(['success'=>'This quantity can be provided',200]);
            }
            else if ($existingQuantity=$requestQuantity) {
                return response()->json(['success' => 'This quantity can be provided. This bulk will be finished with this request.', 200]);
            }
            else {
                return response()->json(['error' => 'Sorry!! We don\'t have that much on this medicine', 200]);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => "Oops!! Something is wrong. The Error is {$th->getMessage()}", 500]);
        }
        
        
    /*sell the items to the customers
    * this could lead to change the existing quantity of each medicine
    * could lead to update the customer_medical_history_details
    
     */
        
    }
    public function sell(Request $request){
      
        $this->validate($request,[
            'customer_id'=>'required',
            'purchase_date'=>'required',
            'mediList'=>'required',

        ]);
        $data=$request->json()->all();

        $userId=$data['customer_id'];
        $customerName = User::where('customer_id', $userId)->value('customer_name');

        $purchase_date=$data["purchase_date"];

        foreach ($data['mediList'] as $medicine ) {


            try {
                $drugName = Inventory::where('batch_no', $medicine['batch_no'])->value('name');
            
                Inventory::where('batch_no', $medicine['batch_no'])->decrement('quantity', $medicine['quantity']);

                // Add customer drug details
                
                
                $drugDetails['customer_id'] = $userId;
                $drugDetails['customer_name'] = $customerName;
                $drugDetails['drug_no'] = $medicine['batch_no'];
                $drugDetails['drug_name'] = $drugName;
                $drugDetails['quantity'] = $medicine['quantity'];
                $drugDetails['purchase_date'] = $purchase_date;


                CustomerDrugDetails::create($drugDetails);

                $feedback[] = "Greate! Medicine details and customer history details for {$drugName} has been updated successfully";

                }
                
             catch (\Throwable $th) {
                $feedback[] = "Oops! Medicine details and customer history details for {$drugName} hasn\'t been updated. Error is {$th->getMessage()}";
            }
        }
        if ($feedback->isEmpty()) {
            return response()->json(['Error'=>'Undefined error. Try again']);
        }
        return response()->json(200);
    }


    


    //Destroy specific medicine 
    public function destroy($id)
    {
        try {

            Inventory::destroy($id);
            return response()->json(["success"=>"All the  details related to the {$id} has been removed ."],200);
        } 
        
        catch (\Throwable $th) {
            return response()->json(['error'=>"Oops ! Error encounting while removing details.Try again. Error was {$th->getMessage()}"],500);
        }
           
    }

  
}
