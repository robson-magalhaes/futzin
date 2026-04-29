<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupPost;
use Illuminate\Http\Request;

class GroupPostController extends Controller
{
    public function index(Group $group)
    {
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $posts = $group->posts()
            ->with('user:id,name,position')
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json($posts);
    }

    public function store(Request $request, Group $group)
    {
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Você não é membro deste grupo'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post = $group->posts()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'type' => 'text',
        ]);

        return response()->json($post->load('user:id,name,position'), 201);
    }

    public function destroy(Group $group, GroupPost $post)
    {
        $user = auth()->user();
        $isAdmin = $group->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();

        if ($post->user_id !== $user->id && !$isAdmin) {
            return response()->json(['message' => 'Sem permissão para deletar este post'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deletado']);
    }
}
