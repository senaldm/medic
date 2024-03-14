<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User login to the system

Route::post('user/login',[UserController::class,'login']);


//admin operations for user in the system

Route::prefix('admin/user')->middleware('auth:api')->group(fn()=>[
    Route::post('/regiter',[UserController::class, 'store']),
    Route::post('/update/{id}',[UserController::class,'update']),

    Route::get('/view',[UserController::class,'view']),
    Route::post('/remove',[UserController::class,'destroy']),
    
]);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
