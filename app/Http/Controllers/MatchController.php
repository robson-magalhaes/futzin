<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Group;
use App\Models\Poll;
use App\Models\PollVote;
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
        abort_if(!$this->isGroupAdmin($match), 403, 'Apenas administradores do grupo podem finalizar partidas.');
        abort_if($match->status === 'finished', 400, 'Partida já foi finalizada.');

        $members = $match->group->members()->get();
        $teams = $match->teams()->orderBy('id')->get();

        return view('matches.finish', compact('match', 'members', 'teams'));
    }

    public function finish(Request $request, FootballMatch $match)
    {
        abort_if(!$this->isGroupAdmin($match), 403, 'Apenas administradores do grupo podem finalizar partidas.');
        abort_if($match->status === 'finished', 400, 'Partida já foi finalizada.');

        $validated = $request->validate([
            'team_a_goals' => 'required|integer|min:0',
            'team_b_goals' => 'required|integer|min:0',
            'penalties' => 'nullable|array',
            'penalties.*' => 'nullable|in:none,yellow_card,red_card',
        ]);

        $teams = $match->teams()->orderBy('id')->get();
        if ($teams->count() < 2) {
            $missing = 2 - $teams->count();
            for ($i = 0; $i < $missing; $i++) {
                Team::create([
                    'match_id' => $match->id,
                    'name' => $i === 0 && $teams->isEmpty() ? 'Time A' : 'Time B',
                    'goals' => 0,
                ]);
            }
            $teams = $match->teams()->orderBy('id')->get();
        }

        $teamA = $teams[0];
        $teamB = $teams[1];

        $teamA->update(['goals' => $validated['team_a_goals']]);
        $teamB->update(['goals' => $validated['team_b_goals']]);

        PlayerPenalty::where('match_id', $match->id)->delete();

        $memberIds = $match->group->members()->pluck('users.id')->map(fn($id) => (string) $id);
        foreach (($validated['penalties'] ?? []) as $userId => $penaltyType) {
            if (!in_array($penaltyType, ['yellow_card', 'red_card'], true)) {
                continue;
            }

            if (!$memberIds->contains((string) $userId)) {
                continue;
            }

            PlayerPenalty::create([
                'user_id' => (int) $userId,
                'match_id' => $match->id,
                'type' => $penaltyType,
                'points_penalty' => $penaltyType === 'yellow_card' ? -1 : -3,
            ]);
        }

        $match->update(['status' => 'finished']);
        $this->calculateRankings($match->group_id);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Partida finalizada! Rankings atualizados.');
    }

    private function calculateRankings(int $groupId): void
    {
        $group   = Group::find($groupId);
        $players = $group->members;
        $cfg     = $group->rankingConfig();

        $finishedMatches = FootballMatch::where('group_id', $groupId)
            ->where('status', 'finished')
            ->with('teams')
            ->get()
            ->keyBy('id');

        $ratingPollIds = Poll::where('group_id', $groupId)
            ->where('status', 'closed')
            ->where('type', 'rating')
            ->pluck('id');

        $mvpPollIds = Poll::where('group_id', $groupId)
            ->where('status', 'closed')
            ->where('type', 'mvp')
            ->pluck('id');

        foreach ($players as $player) {
            $matchIds = PlayerMatch::where('user_id', $player->id)
                ->whereIn('match_id', $finishedMatches->keys())
                ->pluck('match_id')
                ->unique();

            $wins = 0;
            $playerMatches = PlayerMatch::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->get(['match_id', 'team_id']);

            foreach ($playerMatches as $playerMatch) {
                if (!$playerMatch->team_id) {
                    continue;
                }

                $match = $finishedMatches->get($playerMatch->match_id);
                if (!$match || $match->teams->count() < 2) {
                    continue;
                }

                $myTeam = $match->teams->firstWhere('id', $playerMatch->team_id);
                $opponent = $match->teams->firstWhere('id', '!=', $playerMatch->team_id);

                if ($myTeam && $opponent && $myTeam->goals > $opponent->goals) {
                    $wins++;
                }
            }

            $avgRating = PollVote::whereIn('poll_id', $ratingPollIds)
                ->where('candidate_id', $player->id)
                ->avg('rating') ?? 0;

            $mvpVotes = PollVote::whereIn('poll_id', $mvpPollIds)
                ->where('candidate_id', $player->id)
                ->count();

            $penalties = PlayerPenalty::where('user_id', $player->id)
                ->whereIn('match_id', $matchIds)
                ->sum('points_penalty');

            $totalScore = ($avgRating * $cfg['rating_weight'])
                + ($mvpVotes * $cfg['mvp_weight'])
                + ($wins * $cfg['win_weight'])
                + ($penalties * $cfg['penalty_weight']);

            PlayerRanking::updateOrCreate(
                ['user_id' => $player->id, 'group_id' => $groupId],
                [
                    'average_rating' => round((float) $avgRating, 2),
                    'matches_played' => $matchIds->count(),
                    'wins' => $wins,
                    'goals' => 0,
                    'assists' => 0,
                    'mvp_count' => $mvpVotes,
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

    /** Formulário para geração/ajuste de times */
    public function teamsForm(FootballMatch $match)
    {
        $member = $match->group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Acesso negado.');

        $userRole = $member->pivot->role;
        $members  = $match->group->members()->get();
        $teams    = $match->teams()->with('players.user')->get();

        return view('matches.teams', compact('match', 'members', 'teams', 'userRole'));
    }

    /** Gerar times (aleatório geral, por posição ou manual) */
    public function generateTeams(Request $request, FootballMatch $match)
    {
        abort_if(!$this->isGroupAdmin($match), 403, 'Apenas administradores do grupo podem gerenciar times.');

        $validated = $request->validate([
            'method'        => 'required|in:random_general,random_by_position,manual',
            'team_a_name'   => 'required|string|max:100',
            'team_b_name'   => 'required|string|max:100',
            'team_a_players' => 'required_if:method,manual|array',
            'team_b_players' => 'required_if:method,manual|array',
            'team_a_players.*' => 'exists:users,id',
            'team_b_players.*' => 'exists:users,id',
        ]);

        $members = $match->group->members()->get();

        // Remove times anteriores (limpa player_match.team_id e deleta teams)
        PlayerMatch::where('match_id', $match->id)->update(['team_id' => null]);
        $match->teams()->delete();

        $teamA = Team::create(['match_id' => $match->id, 'name' => $validated['team_a_name'], 'goals' => 0]);
        $teamB = Team::create(['match_id' => $match->id, 'name' => $validated['team_b_name'], 'goals' => 0]);

        $method = $validated['method'];

        if ($method === 'random_general') {
            $shuffled = $members->shuffle();
            $half = (int) ceil($shuffled->count() / 2);
            $teamAPlayers = $shuffled->take($half);
            $teamBPlayers = $shuffled->skip($half);
        } elseif ($method === 'random_by_position') {
            // Posições preferidas por time (goleiro, def/lat separados de mid/atk)
            $byPosition = $members->groupBy(fn($u) => $u->position ?: 'sem_posicao');
            $teamAPlayers = collect();
            $teamBPlayers = collect();

            foreach ($byPosition as $position => $playersInPos) {
                $shuffled = $playersInPos->shuffle();
                $half = (int) ceil($shuffled->count() / 2);
                $teamAPlayers = $teamAPlayers->merge($shuffled->take($half));
                $teamBPlayers = $teamBPlayers->merge($shuffled->skip($half));
            }
        } else {
            // Manual
            $teamAPlayers = $members->whereIn('id', $validated['team_a_players'] ?? []);
            $teamBPlayers = $members->whereIn('id', $validated['team_b_players'] ?? []);
        }

        foreach ($teamAPlayers as $player) {
            PlayerMatch::updateOrCreate(
                ['user_id' => $player->id, 'match_id' => $match->id],
                ['team_id' => $teamA->id]
            );
        }

        foreach ($teamBPlayers as $player) {
            PlayerMatch::updateOrCreate(
                ['user_id' => $player->id, 'match_id' => $match->id],
                ['team_id' => $teamB->id]
            );
        }

        return redirect()->route('matches.teams.form', $match)
            ->with('success', 'Times gerados com sucesso!');
    }

    private function isGroupAdmin(FootballMatch $match): bool
    {
        return $match->group->members()
            ->where('user_id', auth()->id())
            ->wherePivot('role', 'admin')
            ->exists();
    }
}
