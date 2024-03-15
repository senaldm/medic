<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// User login to the system

Route::post('user/login',[UserController::class,'login']);


//admin routes for user in the system

Route::prefix('admin/user')->middleware('auth:api')->group(fn()=>[
    
    Route::post('/regiter',[UserController::class, 'store']),
    Route::post('/update-user/{id}',[UserController::class,'update']),
    Route::get('/all-users',[UserController::class,'view']),
    Route::post('/remove-user/{id}',[UserController::class,'destroy']),
    
]);


//Admin routes for medicine items  

Route::prefix('admin/medicine')->middleware('auth:api')->group(fn()=>[

    Route::get('/all-medicine-details',[InventoryController::class,'index']),
    Route::post('/add-medicine',[InventoryController::class,'store']),
    Route::post('/update-medi-details/{id}',[InventoryController::class,'update']),
    Route::post('/remove-medi-details/{id}',[InventoryController::class,'destroy'])

]);

// Cashier routes for medicine items

Route::prefix('cashier/medicine')->middleware('auth:api')->group(fn()=>[

    Route::post('/update-medi-details/{id}',[InventoryController::class,'update']),
    Route::post('/remove-medi-details/{id}',[InventoryController::class,'destroy']),
    
    //  here following routes are related to sell the medicine by cashier to the customer which leads to the update/edit the inventory table and customer_buying_history table

    Route::post('/sell-medicine',[InventoryController::class,'sell']),
    Route::post('/check -quantity -avaliable/{$id}/{$quantity}',[InventoryController::class, 'checkQuatity']),
    Route::post('/check-customer/{$id}',[UserController::class,'checkCustomer']),

]);



//Manager routes for customer details
Route::prefix('manager/user')->middleware('auth:api')->group(fn()=>[
    Route::post('update-customer-details/{id}',[UserController::class,'update']),
    Route::post('remove-customer/{id}',[UserController::class,'destroy']),
]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
