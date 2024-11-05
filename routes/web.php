<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    // return view('welcome');
    return redirect('/sales');
});

Route::get('/dashboard', function () {
    return view('sales.dashboard');
})->middleware(['auth', 'verified'])->name('sales.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/get-provinces', function () {
    $response = Http::get('https://wilayah.id/api/provinces.json');
    return $response->json();
});
Route::get('/get-regencies/{province_code}', function ($province_code) {
    $response = Http::get("https://wilayah.id/api/regencies/{$province_code}.json");
    return $response->json();
});
require __DIR__ . '/auth.php';
