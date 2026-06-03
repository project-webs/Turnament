<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'type',
        'status', 'third_place_match', 'seeded', 'participant_count',
    ];

    protected $casts = [
        'third_place_match' => 'boolean',
        'seeded'            => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tournament $tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = Str::slug($tournament->name) . '-' . Str::random(6);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class)->orderBy('seed')->orderBy('name');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class)->orderBy('round')->orderBy('match_number');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTotalRoundsAttribute(): int
    {
        $count = $this->participant_count;
        if ($count <= 1) return 0;
        return (int) ceil(log($count, 2));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Belum Dimulai',
            'ongoing'  => 'Sedang Berlangsung',
            'finished' => 'Selesai',
            default    => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'gray',
            'ongoing'  => 'green',
            'finished' => 'blue',
            default    => 'gray',
        };
    }

    public function getStandingsAttribute(): \Illuminate\Support\Collection
    {
        if ($this->type !== 'round_robin') return collect();

        $participants = $this->participants->keyBy('id')->map(function ($p) {
            return (object) [
                'id' => $p->id,
                'name' => $p->name,
                'group_name' => $p->group_name,
                'played' => 0,
                'won' => 0,
                'lost' => 0,
                'points' => 0,
                'set_won' => 0,
                'set_lost' => 0,
                'set_diff' => 0,
                'point_won' => 0,
                'point_lost' => 0,
                'point_diff' => 0,
            ];
        });

        foreach ($this->matches as $match) {
            if ($match->status !== 'finished' || $match->is_bye) continue;

            $p1 = $match->participant1_id;
            $p2 = $match->participant2_id;
            $winner = $match->winner_id;

            if ($p1 && isset($participants[$p1])) {
                $participants[$p1]->played++;
                if ($match->score1 !== null) $participants[$p1]->set_won += (int) $match->score1;
                if ($match->score2 !== null) $participants[$p1]->set_lost += (int) $match->score2;
                
                if (is_array($match->point_history)) {
                    foreach ($match->point_history as $setPoints) {
                        $participants[$p1]->point_won += (int) ($setPoints['p1'] ?? 0);
                        $participants[$p1]->point_lost += (int) ($setPoints['p2'] ?? 0);
                    }
                }
            }
            if ($p2 && isset($participants[$p2])) {
                $participants[$p2]->played++;
                if ($match->score2 !== null) $participants[$p2]->set_won += (int) $match->score2;
                if ($match->score1 !== null) $participants[$p2]->set_lost += (int) $match->score1;

                if (is_array($match->point_history)) {
                    foreach ($match->point_history as $setPoints) {
                        $participants[$p2]->point_won += (int) ($setPoints['p2'] ?? 0);
                        $participants[$p2]->point_lost += (int) ($setPoints['p1'] ?? 0);
                    }
                }
            }

            if ($winner) {
                $loser = ($winner == $p1) ? $p2 : $p1;
                if (isset($participants[$winner])) {
                    $participants[$winner]->won++;
                    $participants[$winner]->points += 1;
                }
                if (isset($participants[$loser])) {
                    $participants[$loser]->lost++;
                }
            }
        }

        foreach ($participants as $p) {
            $p->set_diff = $p->set_won - $p->set_lost;
            $p->point_diff = $p->point_won - $p->point_lost;
        }

        // Sort globally first, then group by group_name
        $sorted = $participants->sortBy([
            ['points', 'desc'],
            ['set_diff', 'desc'],
            ['point_diff', 'desc'],
            ['set_won', 'desc'],
            ['point_won', 'desc'],
            ['won', 'desc'],
            ['lost', 'asc'],
        ])->values();

        // Group by group_name, return as collection of groups
        $grouped = $sorted->groupBy('group_name')->map(function ($group, $groupName) {
            $name = (empty($groupName)) ? 'A' : $groupName;
            return (object) [
                'name' => $name,
                'standings' => $group->values()
            ];
        });

        return $grouped->sortBy('name')->values();
    }
}
