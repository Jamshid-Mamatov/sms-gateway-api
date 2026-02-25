<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProviderController;
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

Route::prefix('providers')->group(function () {
    Route::get('/',              [ProviderController::class, 'index']);
    Route::post('/',             [ProviderController::class, 'store']);
    Route::get('/{provider}',    [ProviderController::class, 'show']);
    Route::put('/{provider}',    [ProviderController::class, 'update']);
    Route::delete('/{provider}', [ProviderController::class, 'destroy']);
});

// ── Admin: Projects + Security management ─────────────────────────────────
Route::prefix('projects')->group(function () {
    Route::get('/',                                  [ProjectController::class,  'index']);
    Route::post('/',                                 [ProjectController::class,  'store']);
    Route::get('/{project}',                         [ProjectController::class,  'show']);
    Route::put('/{project}',                         [ProjectController::class,  'update']);
    Route::delete('/{project}',                      [ProjectController::class,  'destroy']);
    Route::post('/{project}/regenerate-key',         [ProjectController::class,  'regenerateKey']);
});
