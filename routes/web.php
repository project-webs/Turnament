<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to tournaments or login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('tournaments.index');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard redirect
    Route::get('/dashboard', fn() => redirect()->route('tournaments.index'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tournaments CRUD
    Route::resource('tournaments', TournamentController::class);
    
    Route::post('/players/calculate-rating', [PlayerController::class, 'calculateRating'])->name('players.calculate-rating');
    Route::resource('players', PlayerController::class)->except(['show']);
    Route::resource('users', UserController::class);

    // Tournament actions
    Route::post('/tournaments/{tournament}/start', [TournamentController::class, 'start'])
        ->name('tournaments.start');
    Route::post('/tournaments/{tournament}/reset-bracket', [TournamentController::class, 'resetBracket'])
        ->name('tournaments.reset-bracket');

    // Participants
    Route::post('/tournaments/{tournament}/participants', [ParticipantController::class, 'store'])
        ->name('participants.store');
    Route::post('/tournaments/{tournament}/participants/bulk', [ParticipantController::class, 'bulkStore'])
        ->name('participants.bulk-store');
    Route::delete('/tournaments/{tournament}/participants/{participant}', [ParticipantController::class, 'destroy'])
        ->name('participants.destroy');

    // Matches
    Route::patch('/matches/{match}', [MatchController::class, 'update'])->name('matches.update');
    Route::post('/matches/{match}/reset', [MatchController::class, 'reset'])->name('matches.reset');
});

require __DIR__ . '/auth.php';
