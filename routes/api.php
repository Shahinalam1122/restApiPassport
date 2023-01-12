<?php

use App\Http\Controllers\UserApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//get api for fetch user
Route::get('/users/{id?}',[UserApiController::class,'showUser']);

// post api for add user
Route::post('/add-user',[UserApiController::class,'addUser']);

// post api for add multiple user
Route::post('/add-multiple-user',[UserApiController::class,'addMultipleUser']);

// put api for update user details
Route::put('/update-user-details/{id}',[UserApiController::class,'updateUserDetails']);

// patch api for update single record
Route::patch('/update-single-record/{id}',[UserApiController::class,'updateSingleRecord']);

// delete api for delete single user
Route::delete('/delete-single-user/{id}',[UserApiController::class,'deleteUser']);

// delete api for delete single user with json
Route::delete('/delete-single-user-with-json',[UserApiController::class,'deleteUserJson']);

// delete api for delete multiple user
Route::delete('/delete-multiple-user/{ids}',[UserApiController::class,'deleteMultipleUser']);

// delete api for delete multiple user with json
Route::delete('/delete-multiple-user-with-json',[UserApiController::class,'deleteMultipleUserJson']);

//register user for passport authentication
Route::post('/register-user-using-passport',[UserApiController::class,'registerUserUsingPassport']);

// login user api for passport authentication
Route::post('/login-user-using-passport',[UserApiController::class,'loginUserUsingPassport']);
