<?php

use App\Http\Controllers\AccessoriesController;
use App\Http\Controllers\DevicesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetsController;
use App\Http\Controllers\VerificationApiController;
use App\Http\Controllers\DevicesTransferController;
use App\Http\Controllers\LeavingWorkController;
use App\Http\Controllers\UserDevicesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\PdfController;

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

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/pdf/download/{id}', [PdfController::class, 'downloadPdf']);

    Route::post('/inventorization', [InventorizationController::class, 'store']);
    Route::get('/inventorization', [InventorizationController::class, 'index']);
    
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/refresh', [LoginController::class, 'refresh']);
    Route::get('/user-profile', [LoginController::class, 'userProfile']);

    Route::get('/userDevices', [UserDevicesController::class, 'index']);
      Route::get('users/devices/count', [DevicesController::class, 'deviceCounts']);

    Route::post('/devices/transfer', [DevicesTransferController::class, 'store']);
    Route::get('/devices/transfer/info/{id}', [DevicesTransferController::class, 'getTranferInfo']);
    Route::get('/devices/transfer/confirm/{id}', [DevicesTransferController::class, 'confirmTransfer']);
    Route::get('/devices/transfer/decline/{id}', [DevicesTransferController::class, 'declineTransfer']);


    Route::get('/devices/{type}', [DevicesController::class, 'index']);
    Route::post('/devices/{type}', [DevicesController::class, 'store']);
    Route::get('/devices/accessories/{type}/{id}', [DevicesController::class, 'getDeviceAccessories']);
    Route::get('/devices/{type}/{id}', [DevicesController::class, 'show']);
    Route::delete('/devices/{type}/{id}', [DevicesController::class, 'destroy']);
    Route::put('/devices/{type}/{id}', [DevicesController::class, 'update']); 
    Route::get('/devices/{type}/{id}/history', [DevicesController::class, 'history']);
  

    Route::get('/users/messages', [MessagesController::class, 'index']);
    Route::get('/users/messages/generalData', [MessagesController::class, 'messagesGeneral']);
    Route::get('/users/messages/count', [MessagesController::class, 'messagesCount']);

    Route::get('/users/devices/count', [UserDevicesController::class, 'devicesCount']);
    Route::get('/users/count', [UsersController::class, 'usersCount']);
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/allUsers', [UsersController::class, 'allUsers']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    
    Route::get('/accessories',[AccessoriesController::class, 'index']);
    Route::get('/accessories/{id}',[AccessoriesController::class, 'show']);
    Route::post('/accessories',[AccessoriesController::class, 'store']);
    Route::put('/accessories/{id}',[AccessoriesController::class, 'update']);
    Route::delete('/accessories/{id}',[AccessoriesController::class, 'destroy']);

    Route::post('/leaveWork',[LeavingWorkController::class, 'store']);
    Route::get('/leaveWork/confirm/{id}',[LeavingWorkController::class, 'confirmLeaveWork']);
    Route::get('/leaveWork/decline/{id}',[LeavingWorkController::class, 'declineLeaveWork']);
 
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('/register', [RegistrationController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']); 
    
});

Route::post('/email/resend', [VerificationApiController::class, 'resend'])->name('verification.resend');
Route::post('/password/forgot', [PasswordResetsController::class, 'forgot']);
Route::post('/password/reset', [PasswordResetsController::class, 'reset'])->name("password.reset");
Route::get('/email/verify/{id}', [VerificationApiController::class, 'verify'])->name('verification.verify');
Route::get('/{any?}', function () {
    return response()->json(["error" => "Not Found"], 404);
});



