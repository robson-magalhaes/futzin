<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupPostController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\PayoutController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(function () {
        // Custom group routes BEFORE resource to avoid conflicts
        Route::post('groups/join', [GroupController::class, 'joinByCode']);

        Route::apiResource('groups', GroupController::class);
        Route::post('groups/{group}/add-member', [GroupController::class, 'addMember']);
        Route::delete('groups/{group}/members/{userId}', [GroupController::class, 'removeMember']);
        Route::post('groups/{group}/confirm-presence', [GroupController::class, 'confirmPresence']);
        Route::post('groups/{group}/generate-code', [GroupController::class, 'generateCode']);
        Route::put('groups/{group}/members/{userId}/promote', [GroupController::class, 'promoteMember']);
        Route::put('groups/{group}/members/{userId}/demote', [GroupController::class, 'demoteMember']);

        // Group feed
        Route::get('groups/{group}/posts', [GroupPostController::class, 'index']);
        Route::post('groups/{group}/posts', [GroupPostController::class, 'store']);
        Route::delete('groups/{group}/posts/{post}', [GroupPostController::class, 'destroy']);

        Route::post('matches', [MatchController::class, 'store']);
        Route::get('matches/{match}', [MatchController::class, 'show']);
        Route::post('matches/{match}/finish', [MatchController::class, 'finishMatch']);

        Route::post('ratings/rate', [RatingController::class, 'ratePlayer']);
        Route::get('ratings/{playerId}/{matchId}', [RatingController::class, 'getPlayerRatings']);

        Route::get('rankings/group/{groupId}', [RankingController::class, 'groupRanking']);
        Route::get('rankings/player/{playerId}/group/{groupId}', [RankingController::class, 'playerStats']);

        Route::get('subscriptions/current', [SubscriptionController::class, 'current']);
        Route::post('subscriptions', [SubscriptionController::class, 'subscribe']);
        Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel']);

        Route::get('payouts/my', [PayoutController::class, 'myPayouts']);
        Route::get('payouts/group/{groupId}', [PayoutController::class, 'groupPayouts']);
        Route::post('payouts/{payout}/mark-paid', [PayoutController::class, 'markAsPaid']);
    });
});
