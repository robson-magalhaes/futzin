<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Group;
use App\Models\PlayerMatch;
use App\Models\PlayerPenalty;
use App\Models\PlayerRanking;
use App\Models\Team;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function create(Request $request)
    {
        $groups = auth()->user()->ownedGroups()->get();
        $selectedGroupId = $request->query('group_id');

        return view('matches.create', compact('groups', 'selectedGroupId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'nullable|string|max:255',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $group = auth()->user()->ownedGroups()->findOrFail($validated['group_id']);
        $match = $group->matches()->create($validated);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Partida agendada com sucesso!');
    }

    public function show(FootballMatch $match)
    {
        $member = $match->group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Acesso negado.');

        $match->load('group', 'players', 'teams', 'penalties.user');
        $userRole = $member->pivot->role;

        return view('matches.show', compact('match', 'userRole'));
    }

    public function finishForm(FootballMatch $match)
    {
        abort_if($match->group->user_id !== auth()->id(), 403, 'Apenas o dono do grupo pode finalizar partidas.');
        abort_if($match->status === 'finished', 400, 'Partida já foi finalizada.');

        $members = $match->group->members()->get();

        return view('matches.finish', compact('match', 'members'));
    }

    public function finish(Request $request, FootballMatch $match)
    {
        abort_if($match->group->user_id !== auth()->id(), 403, 'Apenas o dono do grupo pode finalizar partidas.');
        abort_if($match->status === 'finished', 400, 'Partida já foi finalizada.');

        $validated = $request->validate([
            'team_a_name' => 'required|string|max:100',
            'team_b_name' => 'required|string|max:100',
            'team_a_goals' => 'required|integer|min:0',
            'team_b_goals' => 'required|integer|min:0',
            'players' => 'required|array|min:1',
            'players.*.user_id' => 'required|exists:users,id',
            'players.*.team' => 'required|in:a,b',
            'players.*.goals' => 'required|integer|min:0',
            'players.*.assists' => 'required|integer|min:0',
        ]);

        $teamA = Team::create([
            'match_id' => $match->id,
            'name' => $validated['team_a_name'],
            'goals' => $validated['team_a_goals'],
        ]);

        $teamB = Team::create([
            'match_id' => $match->id,
            'name' => $validated['team_b_name'],
            'goals' => $validated['team_b_goals'],
        ]);

        foreach ($validated['players'] as $idx => $playerData) {
            $teamId = $playerData['team'] === 'a' ? $teamA->id : $teamB->id;
            $isMvp = $request->boolean("players.{$idx}.is_mvp");

            PlayerMatch::updateOrCreate(
                ['user_id' => $playerData['user_id'], 'match_id' => $match->id],
                [
                    'goals' => $playerData['goals'],
                    'assists' => $playerData['assists'],
                    'is_mvp' => $isMvp,
                    'team_id' => $teamId,
                ]
            );

            if ($request->boolean("players.{$idx}.yellow_card")) {
                PlayerPenalty::create([
                    'user_id' => $playerData['user_id'],
                    'match_id' => $match->id,
                    'type' => 'yellow_card',
                    'points_penalty' => -1,
                ]);
            }

            if ($request->boolean("players.{$idx}.red_card")) {
                PlayerPenalty::create([
                    'user_id' => $playerData['user_id'],
                    'match_id' => $match->id,
                    'type' => 'red_card',
                    'points_penalty' => -3,
                ]);
            }
        }

        $match->update(['status' => 'finished']);
        $this->calculateRankings($match->group_id);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Partida finalizada! Rankings atualizados.');
    }

    private function calculateRankings(int $groupId): void
    {
        $group = Group::find($groupId);
        $players = $group->members;

        foreach ($players as $player) {
            $matchIds = $player->matches()
                ->where('matches.group_id', $groupId)
                ->pluck('matches.id');

            $avgRating = $player->ratings()
                ->whereIn('match_id', $matchIds)
                ->avg('rating') ?? 0;

            $totalGoals = PlayerMatch::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->sum('goals');

            $totalAssists = PlayerMatch::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->sum('assists');

            $mvpCount = PlayerMatch::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->where('is_mvp', true)
                ->count();

            $penalties = PlayerPenalty::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->sum('points_penalty');

            $totalScore = ($avgRating * 2) + ($totalGoals * 3) + ($totalAssists * 1.5) + ($mvpCount * 5) + $penalties;

            PlayerRanking::updateOrCreate(
                ['user_id' => $player->id, 'group_id' => $groupId],
                [
                    'average_rating' => round((float) $avgRating, 2),
                    'matches_played' => $matchIds->count(),
                    'goals' => $totalGoals,
                    'assists' => $totalAssists,
                    'mvp_count' => $mvpCount,
                    'points_penalty' => $penalties,
                    'total_score' => round($totalScore, 2),
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
