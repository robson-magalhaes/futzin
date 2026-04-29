<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FootballMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'group_id',
        'scheduled_at',
        'status',
        'location',
        'title',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_match', 'match_id', 'user_id')
            ->withPivot('rating', 'goals', 'assists', 'is_sent_off', 'is_mvp', 'final_score', 'team_id')
            ->withTimestamps();
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'match_id');
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(PlayerPenalty::class, 'match_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(PlayerRating::class, 'match_id');
    }
}