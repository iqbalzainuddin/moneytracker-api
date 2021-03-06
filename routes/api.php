<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;

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

// Public Route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    // Expenses route
    Route::apiResource('expenses', ExpenseController::class);
    // Route::get('/expenses', [ExpenseController::class, 'index']);
    // Route::post('/expenses', [ExpenseController::class, 'store']);

    // Auth related route
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/wallet/{user}', [AuthController::class, 'updWallet']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
