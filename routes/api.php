<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ScrappingController;
use App\Http\Controllers\ScrapController;
use App\Http\Controllers\SupportedCountriesController;
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

// files endpoint
Route::get('/getFilesFromFolder', [FileController::class, 'getFilesFromFolder']);

// scrapping endpoint
Route::get('/data', ScrappingController::class);
Route::get('/supported/countries', SupportedCountriesController::class);

Route::get('/getPrices', [ScrapController::class,'getPrices']);
Route::post('/historyData', [ScrapController::class, 'historyData']);
