<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    protected $fillable = [
        'tournament_id', 'round', 'match_number', 'bracket',
        'participant1_id', 'participant2_id', 'winner_id',
        'score1', 'score2', 'status', 'next_match_id', 'next_match_slot',
        'is_bye', 'point_history', 'is_rating_processed',
    ];

    protected $appends = ['player1_name', 'player2_name'];

    protected $casts = [
        'next_match_slot' => 'boolean',
        'is_bye' => 'boolean',
        'point_history' => 'array',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function participant1(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant1_id');
    }

    public function participant2(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'winner_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(TournamentMatch::class, 'next_match_id');
    }

    public function getPlayer1NameAttribute(): string
    {
        if ($this->participant1) return $this->participant1->name;
        if ($this->is_bye && $this->participant2) return 'BYE';
        return 'TBD';
    }

    public function getPlayer2NameAttribute(): string
    {
        if ($this->participant2) return $this->participant2->name;
        if ($this->is_bye && $this->participant1) return 'BYE';
        return 'TBD';
    }
}
