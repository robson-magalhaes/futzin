<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()
            ->with('owner')
            ->withCount('members')
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        $group = auth()->user()->ownedGroups()->create($validated);
        auth()->user()->groups()->attach($group, ['role' => 'admin']);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Grupo criado com sucesso!');
    }

    public function show(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        $group->load([
            'owner',
            'members',
            'matches' => fn($q) => $q->orderByDesc('scheduled_at')->limit(5),
            'posts' => fn($q) => $q->with('user:id,name,position')->orderByDesc('created_at')->limit(20),
        ]);

        $userRole = $member->pivot->role;
        $rankings = $group->rankings()->with('user:id,name,position')->orderBy('position')->get();

        return view('groups.show', compact('group', 'userRole', 'rankings'));
    }

    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'monthly_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $group->update($validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Grupo atualizado com sucesso!');
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Grupo deletado com sucesso!');
    }

    public function joinByCode(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string|size:6',
        ]);

        $group = Group::where('join_code', strtoupper(trim($validated['join_code'])))->first();

        if (!$group) {
            return back()->withErrors(['join_code' => 'Código inválido ou grupo não encontrado.']);
        }

        if ($group->members()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['join_code' => 'Você já é membro deste grupo.']);
        }

        $group->members()->attach(auth()->id(), ['role' => 'player']);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Entrou no grupo com sucesso!');
    }

    public function addMember(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($group->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->withErrors(['user_id' => 'Usuário já é membro deste grupo.']);
        }

        $group->members()->attach($validated['user_id'], ['role' => 'player']);

        return back()->with('success', 'Membro adicionado com sucesso!');
    }

    public function removeMember(Group $group, User $user)
    {
        $this->authorize('update', $group);
        $group->members()->detach($user->id);

        return back()->with('success', 'Membro removido com sucesso!');
    }

    public function promoteMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['user' => 'Usuário não é membro do grupo.']);
        }

        $group->members()->updateExistingPivot($user->id, ['role' => 'admin']);

        return back()->with('success', "{$user->name} promovido a administrador!");
    }

    public function demoteMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Você não pode rebaixar a si mesmo.']);
        }

        $group->members()->updateExistingPivot($user->id, ['role' => 'player']);

        return back()->with('success', "{$user->name} rebaixado a jogador.");
    }

    public function generateCode(Group $group)
    {
        $this->authorize('update', $group);

        do {
            $code = strtoupper(Str::random(6));
        } while (Group::where('join_code', $code)->where('id', '!=', $group->id)->exists());

        $group->update(['join_code' => $code]);

        return back()->with('success', "Novo código gerado: {$code}");
    }

    public function confirmPresence(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403);

        auth()->user()->groups()->updateExistingPivot($group->id, ['presence_confirmed' => true]);

        return back()->with('success', 'Presença confirmada!');
    }

    public function storePost(Request $request, Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $group->posts()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'type' => 'text',
        ]);

        return back()->with('success', 'Publicado com sucesso!');
    }

    public function destroyPost(Group $group, $postId)
    {
        $post = $group->posts()->findOrFail($postId);
        $user = auth()->user();

        $isAdmin = $group->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_unless($post->user_id === $user->id || $isAdmin, 403, 'Sem permissão.');

        $post->delete();

        return back()->with('success', 'Post removido.');
    }
}
