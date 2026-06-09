<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FriendlyMatchController;
use App\Http\Controllers\Api\FriendlyMatchGameController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\MatchController;

Route::post('/login', [AuthController::class, 'login']);

// Public Players routes
Route::apiResource('players', PlayerController::class)->only(['index', 'show']);

// Public Tournaments routes
Route::apiResource('tournaments', TournamentController::class)->only(['index', 'show']);

// Public Friendly Matches routes
Route::apiResource('friendly-matches', FriendlyMatchController::class)->only(['index', 'show']);
Route::apiResource('friendly-matches.games', FriendlyMatchGameController::class)->scoped()->only(['index', 'show']);


Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('users', UserController::class);

    // Friendly Matches
    Route::apiResource('friendly-matches', FriendlyMatchController::class)->except(['index', 'show']);
    Route::apiResource('friendly-matches.games', FriendlyMatchGameController::class)->scoped()->except(['index', 'show']);

    // Players
    Route::apiResource('players', PlayerController::class)->except(['index', 'show']);

    // Iurans
    Route::apiResource('iurans', \App\Http\Controllers\Api\IuranController::class);

    // Tournaments
    Route::apiResource('tournaments', TournamentController::class)->except(['index', 'show']);
    Route::post('/tournaments/{tournament}/start', [TournamentController::class, 'start']);
    Route::post('/tournaments/{tournament}/reset-bracket', [TournamentController::class, 'resetBracket']);

    // Participants
    Route::apiResource('tournaments.participants', ParticipantController::class)->scoped();

    // Tournament Matches
    Route::apiResource('matches', MatchController::class);
    Route::post('/matches/{match}/reset', [MatchController::class, 'reset']);
});
