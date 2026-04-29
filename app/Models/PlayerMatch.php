<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatch extends Model
{
    protected $table = 'player_match';

    protected $fillable = [
        'user_id',
        'match_id',
        'team_id',
        'rating',
        'goals',
        'assists',
        'is_sent_off',
        'is_mvp',
        'final_score',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'final_score' => 'decimal:2',
        'is_sent_off' => 'boolean',
        'is_mvp' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
