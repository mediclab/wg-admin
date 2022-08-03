<?php

use App\Http\Pages\Dashboard;
use App\Http\Pages\Home;
use Illuminate\Support\Facades\Route;

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

Route::get('/', static function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route(Dashboard::getRouteName());
    }

    return redirect()->route(Home::getRouteName());
})->name('index');
