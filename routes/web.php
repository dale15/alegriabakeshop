<?php

use App\Livewire\Pos\Dashboard;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', Dashboard::class)->name('pos');