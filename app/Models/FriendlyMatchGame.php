<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendlyMatchGame extends Model
{
    protected $fillable = [
        'friendly_match_id',
        'player_id',
        'opponent_name',
        'score_home',
        'score_away',
    ];

    public function friendlyMatch()
    {
        return $this->belongsTo(FriendlyMatch::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
