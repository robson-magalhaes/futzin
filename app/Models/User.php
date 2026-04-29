<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'position', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups')->withPivot('role', 'presence_confirmed')->withTimestamps();
    }

    public function ownedGroups()
    {
        return $this->hasMany(Group::class);
    }

    public function matches()
    {
        return $this->belongsToMany(\App\Models\FootballMatch::class, 'player_match', 'user_id', 'match_id')
            ->withPivot('rating', 'goals', 'assists', 'is_sent_off', 'is_mvp', 'final_score', 'team_id')
            ->withTimestamps();
    }

    public function penalties()
    {
        return $this->hasMany(PlayerPenalty::class);
    }

    public function ratings()
    {
        return $this->hasMany(PlayerRating::class, 'user_id');
    }

    public function ratedBy()
    {
        return $this->hasMany(PlayerRating::class, 'rated_by');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->latest()->first();
    }

    public function rankings()
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription() !== null;
    }
}
