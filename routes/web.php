<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

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


Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');


// Contact routes
Route::resource('contacts', ContactController::class);
Route::post('contacts/merge', [ContactController::class, 'merge'])->name('contacts.merge');
Route::get('contacts/merge-preview', [ContactController::class, 'getMergePreview'])->name('contacts.merge-preview');

// Custom field routes
Route::resource('custom-fields', CustomFieldController::class);

// API routes for AJAX
Route::prefix('api')->group(function () {
    Route::get('contacts', [ContactController::class, 'index'])->name('api.contacts.index');
    Route::post('contacts', [ContactController::class, 'store'])->name('api.contacts.store');
    Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('api.contacts.update');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('api.contacts.destroy');
});

// Authentication Routes
Route::get('login', function () {
    return view('login');
})->name('login');

Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('admin.dashboard');
    }
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::get('register', function () {
    return view('register');
})->name('register');

Route::post('register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    Auth::login($user);
    return redirect('admin.dashboard');
});

Route::post('logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    // Contacts AJAX endpoints
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::put('/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
    Route::post('/contacts/merge', [ContactController::class, 'merge']);
    // Custom Fields AJAX endpoints
    Route::get('/custom-fields', [CustomFieldController::class, 'index']);
    Route::post('/custom-fields', [CustomFieldController::class, 'store']);
    Route::get('/custom-fields/{id}', [CustomFieldController::class, 'show']);
    Route::put('/custom-fields/{id}', [CustomFieldController::class, 'update']);
    Route::delete('/custom-fields/{id}', [CustomFieldController::class, 'destroy']);
});
