<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataResource;
use App\Models\Inventory;
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
     * Store a newly created resource in storage.
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
                return response()->json(['error'=>'OOpsss! Cannot find the medicine ID. It might not be in the database now. Trying again might work.'],500);
            }

            $inventoryID->update($inventoryData);

            return response()->json(['success'=>'Greate! Medicine details has been updated successfully'],200);
        }
        
        catch (\Throwable $th) {
            return response()->json(['error'=>"Sorry!! A Error occurs while medicine details updating. Error was {$th->getMessage()}. Try again."],500);
        }

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
