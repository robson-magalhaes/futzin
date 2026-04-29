<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'monthly_fee',
        'status',
        'join_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            if (!$group->join_code) {
                do {
                    $code = strtoupper(Str::random(6));
                } while (static::where('join_code', $code)->exists());
                $group->join_code = $code;
            }
        });
    }

    protected $casts = [
        'monthly_fee' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_groups')->withPivot('role', 'presence_confirmed')->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(GroupPost::class);
    }
}
