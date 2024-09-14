<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    return view('hello');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware(['web', 'auth.check'])->group(function () 
{
    # 後臺設定
    Route::get('/admin', [AdminController::class, 'adminPage']);

    Route::get('/admin/createvote', function () {
        return view('admin.createvote');
    });

    Route::post('/admin/createvote', [AdminController::class, 'createVoteEvent'])->name('create.vote');
    Route::get('/admin/vote/{event_id}', [AdminController::class, 'getVoteEvent'])->name('admin.vote.get');
    Route::post('/admin/vote/{event_id}/pdf', [AdminController::class, 'generatePDF'])->name('admin.vote.pdf');

    Route::put('/admin/vote/activate', [AdminController::class, 'activateVoteEvent'])->name('activate.vote');
    Route::put('/admin/vote/deactivate', [AdminController::class, 'deactivateVoteEvent'])->name('deactivate.vote');
});

# 投票
Route::get('/vote/{event_id}/{qrcode_string}', [VoteController::class, 'showVotePage']);
Route::post('/vote', [VoteController::class, 'doVote'])->name('vote');;