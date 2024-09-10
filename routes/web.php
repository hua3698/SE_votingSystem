<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

# 後臺設定
Route::get('/admin', [AdminController::class, 'adminPage']);

Route::get('/admin/createvote', function () {
    return view('admin.createvote');
});

Route::post('/admin/createvote', [AdminController::class, 'createVote'])->name('create.vote');
Route::get('/admin/vote/{event_id}', [AdminController::class, 'getVoteEvent'])->name('admin.vote.get');
Route::post('/admin/vote/{event_id}/pdf', [AdminController::class, 'generatePDF'])->name('admin.vote.pdf');

# 投票
Route::get('/vote/{event_id}/{qrcode_string}', [VoteController::class, 'showVotePage']);