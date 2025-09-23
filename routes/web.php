<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;

// Root route - force authentication and redirect to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');

// Optional: Create a public landing page for marketing/info (if needed)
Route::get('/welcome', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Contact Management Routes
Route::middleware(['auth'])->group(function () {
    // Contacts
    Route::resource('contacts', ContactController::class);
    Route::get('contacts/upload/form', [ContactController::class, 'uploadForm'])->name('contacts.upload');
    Route::post('contacts/upload/process', [ContactController::class, 'processUpload'])->name('contacts.process-upload');
    Route::get('contacts/export/csv', [ContactController::class, 'export'])->name('contacts.export');

    // Broadcast
    Route::prefix('broadcast')->name('broadcast.')->group(function () {
        Route::get('/', [BroadcastController::class, 'index'])->name('index');
        Route::post('/send', [BroadcastController::class, 'send'])->name('send');
        Route::post('/send-to-all', [BroadcastController::class, 'sendToAll'])->name('send-to-all');
        Route::post('/send-to-group', [BroadcastController::class, 'sendToGroup'])->name('send-to-group');
        Route::get('/history', [BroadcastController::class, 'history'])->name('history');
        Route::get('/show/{broadcastLog}', [BroadcastController::class, 'show'])->name('show');
        Route::post('/test-connection', [BroadcastController::class, 'testConnection'])->name('test-connection');
        Route::get('/contacts-by-group', [BroadcastController::class, 'getContactsByGroup'])->name('get-contacts-by-group');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/waboxapp', [App\Http\Controllers\SettingsController::class, 'waboxApp'])->name('waboxapp');
        Route::post('/waboxapp', [App\Http\Controllers\SettingsController::class, 'updateWaboxApp'])->name('waboxapp.update');
        Route::post('/waboxapp/test', [App\Http\Controllers\SettingsController::class, 'testWaboxConnection'])->name('waboxapp.test');
        Route::post('/waboxapp/status', [App\Http\Controllers\SettingsController::class, 'checkAccountStatus'])->name('waboxapp.status');
    });

    Route::redirect('settings', 'settings/waboxapp');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
