<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Main')->name('home');
Route::inertia('/deck', 'Deck')->name('deck');
Route::inertia('/configuration', 'Configuration')->name('configuration');

Route::post('/commands', [\App\Http\Controllers\Commands::class, 'create_command'])->name('commands');
Route::get('/commands', [\App\Http\Controllers\Commands::class, 'get_commands'])->name('commands');
Route::get('/command/{id}', [\App\Http\Controllers\Commands::class, 'execute'])->name('commands');
