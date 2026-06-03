<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendlyMatch extends Model
{
    protected $fillable = [
        'user_id',
        'ptm_name',
        'match_date',
        'notes',
        'total_score_home',
        'total_score_away',
    ];

    protected $casts = [
        'match_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function games()
    {
        return $this->hasMany(FriendlyMatchGame::class);
    }
}
