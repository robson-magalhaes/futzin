<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerRanking extends Model
{
    protected $table = 'player_rankings';

    protected $fillable = [
        'user_id',
        'group_id',
        'average_rating',
        'matches_played',
        'goals',
        'assists',
        'mvp_count',
        'points_penalty',
        'total_score',
        'position',
    ];

    protected $casts = [
        'average_rating' => 'decimal:2',
        'points_penalty' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
