<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\PlayerRanking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function show(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        $rankings = PlayerRanking::where('group_id', $group->id)
            ->with('user:id,name,position,avatar_url')
            ->orderBy('position')
            ->get();

        $myRanking = $rankings->where('user_id', auth()->id())->first();

        return view('rankings.show', compact('group', 'rankings', 'myRanking'));
    }
}
