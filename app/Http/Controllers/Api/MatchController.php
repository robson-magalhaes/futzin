<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\PlayerMatch;
use App\Models\PlayerPenalty;
use App\Models\PlayerRanking;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'scheduled_at' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string',
            'title' => 'nullable|string',
        ]);

        $group = auth()->user()->ownedGroups()->findOrFail($validated['group_id']);

        $match = $group->matches()->create($validated);

        return response()->json($match, 201);
    }

    public function show(FootballMatch $match)
    {
        return response()->json($match->load('group', 'players', 'teams', 'penalties'));
    }

    public function finishMatch(Request $request, FootballMatch $match)
    {
        $validated = $request->validate([
            'result' => 'required|array',
            'result.*.user_id' => 'required|exists:users,id',
            'result.*.rating' => 'required|numeric|between:0,10',
            'result.*.goals' => 'required|integer|min:0',
            'result.*.assists' => 'required|integer|min:0',
            'result.*.is_mvp' => 'boolean',
            'result.*.team_id' => 'required|exists:teams,id',
            'penalties' => 'nullable|array',
            'penalties.*.user_id' => 'exists:users,id',
            'penalties.*.type' => 'in:yellow_card,red_card',
        ]);

        $match->update(['status' => 'finished']);

        foreach ($validated['result'] as $result) {
            PlayerMatch::updateOrCreate(
                ['user_id' => $result['user_id'], 'match_id' => $match->id],
                [
                    'rating' => $result['rating'],
                    'goals' => $result['goals'],
                    'assists' => $result['assists'],
                    'is_mvp' => $result['is_mvp'] ?? false,
                    'team_id' => $result['team_id'],
                ]
            );
        }

        if ($validated['penalties'] ?? false) {
            foreach ($validated['penalties'] as $penalty) {
                $points = $penalty['type'] === 'yellow_card' ? -1 : -3;
                PlayerPenalty::create([
                    'user_id' => $penalty['user_id'],
                    'match_id' => $match->id,
                    'type' => $penalty['type'],
                    'points_penalty' => $points,
                ]);
            }
        }

        $this->calculateRankings($match->group_id);

        return response()->json(['message' => 'Partida finalizada', 'match' => $match]);
    }

    public function calculateRankings($groupId)
    {
        $group = \App\Models\Group::find($groupId);
        $players = $group->members;

        foreach ($players as $player) {
            $matches = $player->matches()->where('group_id', $groupId)->get();

            $avgRating = $player->ratings()
                ->whereIn('match_id', $matches->pluck('id'))
                ->avg('rating') ?? 0;

            $totalGoals = $player->matches()
                ->where('group_id', $groupId)
                ->sum('player_match.goals') ?? 0;

            $totalAssists = $player->matches()
                ->where('group_id', $groupId)
                ->sum('player_match.assists') ?? 0;

            $mvpCount = $player->matches()
                ->where('group_id', $groupId)
                ->where('player_match.is_mvp', true)
                ->count();

            $penalties = $player->penalties()
                ->whereIn('match_id', $matches->pluck('id'))
                ->sum('points_penalty') ?? 0;

            $totalScore = ($avgRating * 2) + ($totalGoals * 3) + ($totalAssists * 1.5) + ($mvpCount * 5) + $penalties;

            PlayerRanking::updateOrCreate(
                ['user_id' => $player->id, 'group_id' => $groupId],
                [
                    'average_rating' => $avgRating,
                    'matches_played' => $matches->count(),
                    'goals' => $totalGoals,
                    'assists' => $totalAssists,
                    'mvp_count' => $mvpCount,
                    'points_penalty' => $penalties,
                    'total_score' => $totalScore,
                ]
            );
        }

        $rankings = PlayerRanking::where('group_id', $groupId)
            ->orderByDesc('total_score')
            ->get();

        foreach ($rankings as $index => $ranking) {
            $ranking->update(['position' => $index + 1]);
        }
    }
}
