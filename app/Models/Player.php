<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = ['user_id', 'name', 'gender', 'nik', 'address', 'division', 'itr_rating', 'match_played', 'win_count', 'lose_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function iurans(): HasMany
    {
        return $this->hasMany(Iuran::class);
    }
}
