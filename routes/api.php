<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\SmsMessageController;
use App\Http\Middleware\AuthenticateApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//providers
Route::prefix('providers')->group(function () {
    Route::get('/',              [ProviderController::class, 'index']);
    Route::post('/',             [ProviderController::class, 'store']);
    Route::get('/{provider}',    [ProviderController::class, 'show']);
    Route::put('/{provider}',    [ProviderController::class, 'update']);
    Route::delete('/{provider}', [ProviderController::class, 'destroy']);
});

// projects
Route::prefix('projects')->group(function () {
    Route::get('/',                                  [ProjectController::class,  'index']);
    Route::post('/',                                 [ProjectController::class,  'store']);
    Route::get('/{project}',                         [ProjectController::class,  'show']);
    Route::put('/{project}',                         [ProjectController::class,  'update']);
    Route::delete('/{project}',                      [ProjectController::class,  'destroy']);
});

//sms sending
Route::middleware(AuthenticateApiKey::class)->prefix('sms')->group(function () {
    Route::post('/send',        [SmsMessageController::class, 'send']);
    Route::get('/history',      [SmsMessageController::class, 'history']);
    Route::get('/{smsMessage}', [SmsMessageController::class, 'show']);
});
