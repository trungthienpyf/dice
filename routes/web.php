<?php

use App\Http\Controllers\DiceConfigController;
use App\Http\Controllers\DiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dice', [DiceController::class, 'index'])->name('dice.index');
Route::get('/dice/create', [DiceController::class, 'create'])->name('dice.create');
Route::get('/dice/{id}', [DiceController::class, 'show'])->name('dice.show');
Route::get('/dice/edit/{dice}', [DiceController::class, 'edit'])->name('dice.edit');
Route::put('/dice/update/{dice}', [DiceController::class, 'updateDice'])->name('dice.update');

Route::post('/dice/verify-password', [DiceController::class, 'verifyPassword'])->name('dice.verify-password');
Route::post('/dice', [DiceController::class, 'store'])->name('dice.store');
Route::get('/api/dice/{id}', [DiceController::class, 'get'])->name('api.dice.get');
Route::get('/api/dice/fetch/{id}', [DiceController::class, 'getFromId'])->name('api.dice.fetch');
Route::post('/api/dice/create-table', [DiceController::class, 'newTable'])->name('api.new-table');
Route::post('/api/dice/{row_id}', [DiceController::class, 'update'])->name('api.dice.update');
Route::post('/api/dice/unlock/{row_id}', [DiceController::class, 'unLockRow'])->name('api.dice.unlock');
Route::delete('/dice/{id}', [DiceController::class, 'destroy'])->name('dice.destroy');

// Dice Config Routes
Route::prefix('dice/configs')->name('dice.configs.')->group(function () {
    Route::get('/list', [DiceConfigController::class, 'index'])->name('index');
    Route::get('/create', [DiceConfigController::class, 'create'])->name('create');
    Route::post('/', [DiceConfigController::class, 'store'])->name('store');
    Route::get('/{config}/edit', [DiceConfigController::class, 'edit'])->name('edit');
    Route::put('/{config}', [DiceConfigController::class, 'update'])->name('update');
    Route::delete('/{config}', [DiceConfigController::class, 'destroy'])->name('destroy');
    Route::post('/{config}/toggle-active', [DiceConfigController::class, 'toggleActive'])->name('toggle-active');
});


