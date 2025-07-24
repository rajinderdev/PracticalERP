<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

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


Route::get('/', [HomeController::class, 'index']);

Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

Route::resource('contacts', ContactController::class);
Route::resource('custom-fields', CustomFieldController::class);
Route::post('/contacts/merge', [ContactController::class, 'merge']);
Route::get('/contacts/merge-preview', [ContactController::class, 'getMergePreview'])->name('contacts.merge-preview');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
