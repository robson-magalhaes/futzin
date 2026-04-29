<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerRanking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function groupRanking($groupId)
    {
        $rankings = PlayerRanking::where('group_id', $groupId)
            ->with('user')
            ->orderBy('position')
            ->get();

        return response()->json($rankings);
    }

    public function playerStats($playerId, $groupId)
    {
        $ranking = PlayerRanking::where('user_id', $playerId)
            ->where('group_id', $groupId)
            ->with('user')
            ->first();

        if (!$ranking) {
            return response()->json(['message' => 'Jogador não encontrado neste grupo'], 404);
        }

        return response()->json($ranking);
    }
}
