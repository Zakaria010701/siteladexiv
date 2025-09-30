<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Frontend\FrontendAppointmentTypeController;
use App\Http\Controllers\Api\Frontend\FrontendBranchController;
use App\Http\Controllers\Api\Frontend\FrontendCategoryController;
use App\Http\Controllers\Api\Frontend\FrontendCreateAppointmentController;
use App\Http\Controllers\Api\Frontend\FrontendCreateWaitingListController;
use App\Http\Controllers\Api\Frontend\FrontendDurationController;
use App\Http\Controllers\Api\Frontend\FrontendOpenController;
use App\Http\Controllers\Api\Frontend\FrontendOpenListController;
use App\Http\Controllers\Api\Frontend\FrontendPackageController;
use App\Http\Controllers\Api\Frontend\FrontendProvidersController;
use App\Http\Controllers\Api\Frontend\FrontendServiceController;
use App\Http\Controllers\Api\Frontend\FrontendSettingsController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServicePackageController;
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
// Offene Routen ohne Auth
Route::post('frontend/auth/email-check', [AuthController::class, 'checkEmailExistence']);
Route::post('frontend/auth/login', [AuthController::class, 'login']); 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('branches', BranchController::class)->middleware(['abilities:branches']);
    Route::apiResource('categories', CategoryController::class)->middleware(['abilities:categories']);
    Route::apiResource('services', ServiceController::class)->middleware(['abilities:services']);
    Route::apiResource('packages', ServicePackageController::class)->middleware(['abilities:packages']);

    Route::group(['prefix' => 'frontend/', 'middleware' => ['abilities:frontend']], function () {
        Route::get('branches', FrontendBranchController::class)->middleware(['abilities:branches']);
        Route::get('categories', FrontendCategoryController::class)->middleware(['abilities:categories']);
        Route::get('appointment-types', FrontendAppointmentTypeController::class)->middleware(['abilities:appointment-type']);
        Route::get('services', FrontendServiceController::class)->middleware(['abilities:services']);
        Route::get('packages', FrontendPackageController::class)->middleware(['abilities:packages']);

        Route::get('calculate/duration', FrontendDurationController::class)->middleware(['abilities:duration']);
        Route::get('providers/available', FrontendProvidersController::class)->middleware(['abilities:users']);

        Route::get('settings', FrontendSettingsController::class)->middleware(['abilities:settings']);

        Route::get('open/list', FrontendOpenListController::class)->middleware(['abilities:appointments']);
        Route::get('open', FrontendOpenController::class)->middleware(['abilities:appointments']);
        Route::post('appointment', FrontendCreateAppointmentController::class)->middleware(['abilities:appointments']);
        Route::post('waiting-list', FrontendCreateWaitingListController::class)->middleware(['abilities:appointments']);
    });
});