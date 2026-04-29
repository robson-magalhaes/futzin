<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerPenalty extends Model
{
    protected $table = 'player_penalties';

    protected $fillable = [
        'user_id',
        'match_id',
        'type',
        'points_penalty',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function getPointsPenaltyAttribute()
    {
        if ($this->type === 'yellow_card') {
            return -1;
        }
        return -3;
    }
}
