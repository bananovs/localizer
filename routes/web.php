<?php

use App\Http\Controllers\MainController;
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

Route::get('/', [MainController::class, 'index']);
Route::post('/', [MainController::class, 'store'])->name('index.store');
Route::get('/list', [MainController::class, 'list'])->name('index.list');
Route::get('/{hash}', [MainController::class, 'show'])->name('index.show');
Route::post('/{hash}/download', [MainController::class, 'download'])->name('index.download');
Route::get('/{hash}/deepl', [MainController::class, 'deepl'])->name('index.deepl');
Route::post('/{hash}/store', [MainController::class, 'storeItem'])->name('index.store.item');
Route::post('/{hash}/add', [MainController::class, 'addItem'])->name('index.store.item.add');
Route::post('/{hash}/delete', [MainController::class, 'destroyItem'])->name('index.store.item.delete');
