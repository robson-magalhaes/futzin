<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $groups = $user->groups()->with('owner')->withCount('members')->get();

        $upcomingMatches = FootballMatch::whereIn('group_id', $groups->pluck('id'))
            ->where('status', 'pending')
            ->where('scheduled_at', '>=', now())
            ->with('group')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recentMatches = FootballMatch::whereIn('group_id', $groups->pluck('id'))
            ->where('status', 'finished')
            ->with('group')
            ->orderByDesc('scheduled_at')
            ->limit(5)
            ->get();

        $subscription = $user->activeSubscription();

        $stats = [
            'groups' => $groups->count(),
            'upcoming_matches' => $upcomingMatches->count(),
            'total_goals' => $user->matches()->sum('player_match.goals'),
            'total_mvps' => $user->matches()->where('player_match.is_mvp', true)->count(),
        ];

        return view('dashboard.index', compact('groups', 'upcomingMatches', 'recentMatches', 'subscription', 'stats'));
    }
}
