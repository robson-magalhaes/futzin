@extends('layouts.app')

@section('title', 'Gestao de Jogadores')
@section('page-title', 'Gestao de Jogadores')
@section('breadcrumb', 'Grupos / ' . $group->name . ' / Jogadores')

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('groups.show', $group) }}" class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Voltar ao Grupo</a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-3">Jogadores do Grupo</h3>
        <div class="space-y-2">
            @foreach($group->members as $member)
            <div class="flex items-center justify-between bg-slate-800/70 rounded-lg px-4 py-3">
                <div>
                    <p class="text-sm text-slate-100">{{ $member->name }}</p>
                    <p class="text-xs {{ $member->pivot->role === 'admin' ? 'text-amber-400' : 'text-slate-500' }}">{{ $member->pivot->role }}</p>
                </div>

                @if($member->pivot->role === 'player' && $member->id !== auth()->id())
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('groups.members.remove', [$group, $member]) }}" onsubmit="return confirm('Remover este jogador do grupo?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Remover</button>
                    </form>
                    <form method="POST" action="{{ route('groups.members.block', [$group, $member]) }}" onsubmit="return confirm('Bloquear este jogador de entrar no grupo?');">
                        @csrf
                        <button class="text-xs text-amber-400 hover:text-amber-300">Bloquear</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-3">Jogadores Bloqueados</h3>
        @if($group->blockedUsers->isEmpty())
        <p class="text-slate-500 text-sm">Nenhum jogador bloqueado.</p>
        @else
        <div class="space-y-2">
            @foreach($group->blockedUsers as $blocked)
            <div class="flex items-center justify-between bg-slate-800/70 rounded-lg px-4 py-3">
                <span class="text-sm text-slate-100">{{ $blocked->name }}</span>
                <form method="POST" action="{{ route('groups.members.unblock', [$group, $blocked]) }}">
                    @csrf
                    <button class="text-xs text-emerald-400 hover:text-emerald-300">Desbloquear</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
