<?php

use App\Livewire\Pos\Dashboard;
use App\Livewire\Pos\MyReceipt;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', Dashboard::class)->name('pos');
Route::get('/receipt/{saleId}', MyReceipt::class)->name('receipt');
