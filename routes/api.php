<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FriendlyMatchController;
use App\Http\Controllers\Api\FriendlyMatchGameController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\MatchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Friendly Matches
    Route::apiResource('friendly-matches', FriendlyMatchController::class);
    Route::post('/friendly-matches/{friendly_match}/games', [FriendlyMatchGameController::class, 'store']);
    Route::delete('/friendly-matches/{friendly_match}/games/{game}', [FriendlyMatchGameController::class, 'destroy']);

    // Players
    Route::apiResource('players', PlayerController::class);

    // Tournaments
    Route::apiResource('tournaments', TournamentController::class);
    Route::post('/tournaments/{tournament}/start', [TournamentController::class, 'start']);
    Route::post('/tournaments/{tournament}/reset-bracket', [TournamentController::class, 'resetBracket']);

    // Participants
    Route::post('/tournaments/{tournament}/participants', [ParticipantController::class, 'store']);
    Route::delete('/tournaments/{tournament}/participants/{participant}', [ParticipantController::class, 'destroy']);

    // Tournament Matches
    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{match}', [MatchController::class, 'show']);
    Route::put('/matches/{match}', [MatchController::class, 'update']);
    Route::post('/matches/{match}/reset', [MatchController::class, 'reset']);
});
