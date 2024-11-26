<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CandidateController;
use App\Http\Middleware\CheckIfAuthenticated;
use App\Http\Middleware\UserLogin;

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

Route::get('/index', function () {
    return view('front.index');
});

Route::get('/user/register', function() {
    return view('front.user.register');
});
Route::post('/user/register', [AuthController::class, 'userRegister'])->name('user.register');
Route::get('/user/login', [AuthController::class, 'showUserLogin'])->name('user.login.form');
Route::post('/user/login', [AuthController::class, 'userLogin'])->name('user.login');
Route::get('/user/logout', [AuthController::class, 'userLogout'])->name('user.logout');

# 投票
Route::middleware([UserLogin::class])->group(function () 
{
    Route::get('/user', [UserController::class, 'userPage']);

    Route::post('/vote', [VoteController::class, 'doVote'])->name('vote');
    Route::get('/vote/{event_id}/{qrcode_string}', [VoteController::class, 'showVotePage']);
    Route::get('/vote/{event_id}/{qrcode_string}/result', [VoteController::class, 'showVoteResult'])->name('vote.result');
});

Route::get('/vote', function() {
    return view('front.temp');
});
Route::get('/vote/candidate', [VoteController::class, 'showAllCandidate'])->name('vote.candidate');


##########################################################################
# 後台
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([CheckIfAuthenticated::class])->group(function () 
{
    Route::get('/outstand', [AdminController::class, 'adminPage']);

    Route::get('/outstand/admin/createadmin', function () {
        return view('admin.createadmin');
    });

    Route::get('/outstand/admin/list', [AdminController::Class, 'adminList']);
    Route::post('/outstand/admin', [AdminController::Class, 'createAdmin'])->name('create.admin');
    Route::put('/outstand/admin', [AdminController::Class, 'updateAdmin'])->name('update.admin');
    Route::delete('/outstand/admin', [AdminController::Class, 'deleteAdmin'])->name('delete.admin');

    Route::get('/outstand/user/list', [UserController::Class, 'userList']);
    Route::delete('/outstand/user', [UserController::Class, 'deleteUser'])->name('delete.user');

    Route::get('/outstand/vote/{event_id}', [VoteController::class, 'getVoteEvent'])
            ->where('event_id', '[0-9]+')
            ->name('admin.vote.get');
    Route::get('/outstand/vote/create', function () {
        return view('admin.vote.create');
    });
    Route::post('/outstand/vote/createvote', [VoteController::class, 'createVoteEvent'])->name('create.vote');
    Route::get('/outstand/vote/{event_id}/edit', [VoteController::class, 'voteEventEditPage'])->name('vote.edit.page');
    Route::put('/outstand/vote/{event_id}/edit', [VoteController::class, 'editVoteEvent'])->name('vote.edit');
    Route::post('/outstand/vote/{event_id}/pdf', [VoteController::class, 'generatePDF'])->name('admin.vote.pdf');
    Route::get('/outstand/vote/{event_id}/check', [VoteController::class, 'checkVoteSituation'])->name('vote.check');
    Route::post('/outstand/vote/{event_id}/check', [VoteController::class, 'postCheckVoteSituation'])->name('vote.check.post');
    Route::get('/outstand/vote/{event_id}/result', [VoteController::class, 'getVoteResult'])->name('admin.vote.result');
    Route::put('/outstand/vote/delete', [VoteController::class, 'deleteVoteEvent'])->name('del.vote');
    Route::put('/outstand/vote/activate', [VoteController::class, 'activateVoteEvent'])->name('activate.vote');
    Route::put('/outstand/vote/deactivate', [VoteController::class, 'deactivateVoteEvent'])->name('deactivate.vote');

    Route::get('/outstand/vote/{event_id}/pdf', [VoteController::class, 'testPDF'])->name('test.pdf');
    Route::get('/outstand/vote/{event_id}/export/detail', [VoteController::class, 'exportDetail'])->name('export.detail');

});
