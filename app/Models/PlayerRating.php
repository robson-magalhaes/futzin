<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerRating extends Model
{
    protected $table = 'player_ratings';

    protected $fillable = [
        'user_id',
        'rated_by',
        'match_id',
        'rating',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }
}
