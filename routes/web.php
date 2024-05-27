<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrlController;
use App\Models\Crl;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CrlController::class,'index']);
Route::get('/api', [CrlController::class,'show']);

Route::get('/json', function () {return response()->json(Crl::all());});

Route::get('/api/{code}', [CrlController::class,'IR']);