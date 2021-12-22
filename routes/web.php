<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
Route::group(['middleware' => 'auth'], function () {
    // Route::get('/home', [App\Http\Controllers\PageController::class, 'Home'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\PageController::class, 'Home'])->name('dashboard');
    Route::get('/api', [App\Http\Controllers\PageController::class, 'HomeChart'])->name('homeChart');
    Route::get('/week-chart/{id}', [App\Http\Controllers\PageController::class, 'DetailWeekChart'])->name('detailWeekChart');
    // Route::get('/weekly', [App\Http\Controllers\PageController::class, 'Week'])->name('week');
    Route::get('/api-week', [App\Http\Controllers\PageController::class, 'WeekChart'])->name('weekChart');
    Route::get('/detail', [App\Http\Controllers\PageController::class, 'Detail'])->name('detail');
    Route::get('/year/{year}', [App\Http\Controllers\PageController::class, 'Year'])->name('year');
    Route::get('/month/{mon}', [App\Http\Controllers\PageController::class, 'Month'])->name('month');
    Route::get('/week/{week}', [App\Http\Controllers\PageController::class, 'Week'])->name('week');
    Route::get('/day/{day}', [App\Http\Controllers\PageController::class, 'Day'])->name('day');
});
Route::get('/api-detail', [App\Http\Controllers\PageController::class, 'ApiDetail'])->name('api-detail');
Route::get('/api-year/{year}', [App\Http\Controllers\PageController::class, 'ApiYear'])->name('api-year');
Route::get('/api-month/{mon}', [App\Http\Controllers\PageController::class, 'ApiMonth'])->name('api-month');
Route::get('/api-week/{week}', [App\Http\Controllers\PageController::class, 'ApiWeek'])->name('api-week');
Route::get('/api-day/{day}', [App\Http\Controllers\PageController::class, 'ApiDay'])->name('api-day');

Route::get('/test/{param}', [App\Http\Controllers\PageController::class, 'ApiDay'])->name('api-day');
