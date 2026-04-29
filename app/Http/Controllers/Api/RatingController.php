<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerRating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function ratePlayer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'match_id' => 'required|exists:matches,id',
            'rating' => 'required|integer|between:1,10',
        ]);

        $existing = PlayerRating::where([
            'user_id' => $validated['user_id'],
            'rated_by' => auth()->id(),
            'match_id' => $validated['match_id'],
        ])->first();

        if ($existing) {
            $existing->update(['rating' => $validated['rating']]);
            return response()->json(['message' => 'Avaliação atualizada', 'rating' => $existing]);
        }

        $rating = PlayerRating::create([
            'user_id' => $validated['user_id'],
            'rated_by' => auth()->id(),
            'match_id' => $validated['match_id'],
            'rating' => $validated['rating'],
        ]);

        return response()->json(['message' => 'Jogador avaliado', 'rating' => $rating], 201);
    }

    public function getPlayerRatings($playerId, $matchId)
    {
        $ratings = PlayerRating::where('user_id', $playerId)
            ->where('match_id', $matchId)
            ->with('ratedBy')
            ->get();

        return response()->json($ratings);
    }
}
