<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()->with('owner')->get();
        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        $group = auth()->user()->ownedGroups()->create($validated);

        auth()->user()->groups()->attach($group, ['role' => 'admin']);

        return response()->json($group, 201);
    }

    public function show(Group $group)
    {
        return response()->json($group->load('owner', 'members', 'matches'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'monthly_fee' => 'numeric|min:0',
            'status' => 'in:active,inactive',
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $group->delete();

        return response()->json(['message' => 'Grupo deletado com sucesso']);
    }

    public function addMember(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($group->members()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json(['message' => 'Usuário já é membro deste grupo'], 422);
        }

        $group->members()->attach($validated['user_id'], ['role' => 'player']);

        return response()->json(['message' => 'Membro adicionado ao grupo']);
    }

    public function removeMember(Group $group, $userId)
    {
        $this->authorize('update', $group);

        $group->members()->detach($userId);

        return response()->json(['message' => 'Membro removido do grupo']);
    }

    public function confirmPresence(Group $group)
    {
        auth()->user()->groups()->updateExistingPivot($group->id, ['presence_confirmed' => true]);

        return response()->json(['message' => 'Presença confirmada']);
    }

    public function joinByCode(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string',
        ]);

        $group = Group::where('join_code', strtoupper(trim($validated['join_code'])))->firstOrFail();

        if ($group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Você já é membro deste grupo'], 422);
        }

        $group->members()->attach(auth()->id(), ['role' => 'player']);

        return response()->json([
            'message' => 'Entrou no grupo com sucesso!',
            'group' => $group->load('owner', 'members'),
        ]);
    }

    public function promoteMember(Group $group, $userId)
    {
        $this->authorize('update', $group);

        if (!$group->members()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Usuário não é membro do grupo'], 422);
        }

        $group->members()->updateExistingPivot($userId, ['role' => 'admin']);

        return response()->json(['message' => 'Membro promovido a admin']);
    }

    public function demoteMember(Group $group, $userId)
    {
        $this->authorize('update', $group);

        if ((int) $userId === auth()->id()) {
            return response()->json(['message' => 'Você não pode se rebaixar'], 422);
        }

        $group->members()->updateExistingPivot($userId, ['role' => 'player']);

        return response()->json(['message' => 'Membro rebaixado a jogador']);
    }

    public function generateCode(Group $group)
    {
        $this->authorize('update', $group);

        do {
            $code = strtoupper(Str::random(6));
        } while (Group::where('join_code', $code)->where('id', '!=', $group->id)->exists());

        $group->update(['join_code' => $code]);

        return response()->json(['join_code' => $group->join_code]);
    }
}
