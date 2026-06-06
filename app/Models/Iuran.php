<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Iuran extends Model
{
    protected $fillable = ['player_id', 'tanggal', 'period', 'amount', 'notes'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'period' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
