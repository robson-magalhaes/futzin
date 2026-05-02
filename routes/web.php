<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/join', [GroupController::class, 'joinByCode'])->name('groups.join');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::get('/groups/{group}/members-management', [GroupController::class, 'membersManagement'])->name('groups.members.manage');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Group member management
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.members.add');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.members.remove');
    Route::post('/groups/{group}/members/{user}/promote', [GroupController::class, 'promoteMember'])->name('groups.members.promote');
    Route::post('/groups/{group}/members/{user}/demote', [GroupController::class, 'demoteMember'])->name('groups.members.demote');
    Route::post('/groups/{group}/members/{user}/block', [GroupController::class, 'blockMember'])->name('groups.members.block');
    Route::post('/groups/{group}/members/{user}/unblock', [GroupController::class, 'unblockMember'])->name('groups.members.unblock');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::post('/groups/{group}/generate-code', [GroupController::class, 'generateCode'])->name('groups.generate-code');
    Route::post('/groups/{group}/confirm-presence', [GroupController::class, 'confirmPresence'])->name('groups.confirm-presence');
    Route::post('/groups/{group}/start-round', [GroupController::class, 'startRound'])->name('groups.start-round');
    Route::post('/groups/{group}/report-daily-payment', [GroupController::class, 'reportDailyPayment'])->name('groups.report-daily-payment');

    // Group posts
    Route::post('/groups/{group}/posts', [GroupController::class, 'storePost'])->name('groups.posts.store');
    Route::delete('/groups/{group}/posts/{post}', [GroupController::class, 'destroyPost'])->name('groups.posts.destroy');

    // Matches
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    Route::get('/matches/{match}/finish', [MatchController::class, 'finishForm'])->name('matches.finish.form');
    Route::post('/matches/{match}/finish', [MatchController::class, 'finish'])->name('matches.finish');
    Route::get('/matches/{match}/teams', [MatchController::class, 'teamsForm'])->name('matches.teams.form');
    Route::post('/matches/{match}/teams', [MatchController::class, 'generateTeams'])->name('matches.teams.generate');

    // Polls (Enquetes)
    Route::post('/groups/{group}/polls', [PollController::class, 'store'])->name('polls.store');
    Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
    Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');
    Route::post('/polls/{poll}/close', [PollController::class, 'close'])->name('polls.close');

    // Rankings
    Route::get('/groups/{group}/rankings', [RankingController::class, 'show'])->name('rankings.show');

    // Subscriptions
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{payout}/mark-paid', [PayoutController::class, 'markAsPaid'])->name('payouts.mark-paid');
    Route::get('/groups/{group}/payouts', [PayoutController::class, 'groupPayouts'])->name('payouts.group');
});
