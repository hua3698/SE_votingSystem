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
    Route::get('/outstand', [AdminController::class, 'adminPage']);

    Route::get('/outstand/createvote', function () {
        return view('admin.createvote');
    });

    Route::post('/outstand/createvote', [AdminController::class, 'createVoteEvent'])->name('create.vote');
    Route::get('/outstand/vote/{event_id}', [AdminController::class, 'getVoteEvent'])->name('admin.vote.get');
    Route::post('/outstand/vote/{event_id}/pdf', [AdminController::class, 'generatePDF'])->name('admin.vote.pdf');
    Route::get('/outstand/vote/{event_id}/check', [AdminController::class, 'checkVoteSituation'])->name('admin.vote.check');
    Route::post('/outstand/vote/{event_id}/check', [AdminController::class, 'postCheckVoteSituation'])->name('admin.vote.check.post');
    Route::get('/outstand/vote/{event_id}/result', [AdminController::class, 'getVoteResult'])->name('admin.vote.result');
    Route::get('/outstand/vote/{event_id}/edit', [AdminController::class, 'editVote'])->name('admin.vote.edit');

    Route::put('/outstand/vote/activate', [AdminController::class, 'activateVoteEvent'])->name('activate.vote');
    Route::put('/outstand/vote/deactivate', [AdminController::class, 'deactivateVoteEvent'])->name('deactivate.vote');

    Route::get('/outstand/vote/{event_id}/pdf', [AdminController::class, 'testPDF'])->name('test.pdf');
});

# 投票
Route::get('/vote/{event_id}/{qrcode_string}', [VoteController::class, 'showVotePage']);
Route::post('/vote', [VoteController::class, 'doVote'])->name('vote');
Route::get('/vote/{event_id}/{qrcode_string}/result', [VoteController::class, 'showVoteResult'])->name('vote.result');