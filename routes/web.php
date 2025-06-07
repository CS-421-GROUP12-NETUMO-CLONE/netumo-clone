<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('targets')->name('targets.')->group(function () {
   Volt::route('index', 'targets.index')->name('index');
   Volt::route('create', 'targets.create')->name('create');


   Volt::route('latest-status/{id}', 'targets.status')->name('status');
   Volt::route('history/{id}', 'targets.history')->name('history');

});

Volt::route('alerts', 'targets.alerts')->name('alerts');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
