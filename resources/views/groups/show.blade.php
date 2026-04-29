@extends('layouts.app')

@section('title', $group->name)
@section('page-title', $group->name)
@section('breadcrumb', 'Grupos / ' . $group->name)

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('matches.create', ['group_id' => $group->id]) }}" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-4 py-2 rounded-lg">Agendar Partida</a>
    @if($userRole === 'admin')
    <a href="{{ route('groups.edit', $group) }}" class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Editar Grupo</a>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-slate-400 text-sm">{{ $group->description ?: 'Sem descrição cadastrada.' }}</p>
                    <p class="text-xs text-slate-500 mt-2">Mensalidade: R$ {{ number_format((float) $group->monthly_fee, 2, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Código de entrada</p>
                    <p class="font-mono text-emerald-400 tracking-wider text-lg">{{ $group->join_code }}</p>
                </div>
            </div>
            <div class="mt-4 flex gap-3 flex-wrap">
                <form method="POST" action="{{ route('groups.confirm-presence', $group) }}">@csrf
                    <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm px-3 py-2 rounded-lg">Confirmar presença</button>
                </form>
                <a href="{{ route('rankings.show', $group) }}" class="bg-slate-800 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg">Ver ranking completo</a>
                <a href="{{ route('payouts.group', $group) }}" class="bg-slate-800 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg">Pagamentos do grupo</a>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold mb-3">Membros ({{ $group->members->count() }})</h3>
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                @foreach($group->members as $member)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-200 truncate">{{ $member->name }}</span>
                    <span class="text-xs {{ $member->pivot->role === 'admin' ? 'text-amber-400' : 'text-slate-500' }}">{{ $member->pivot->role }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold mb-3">Últimas Partidas</h3>
            @if($group->matches->isEmpty())
            <p class="text-slate-500 text-sm">Nenhuma partida registrada.</p>
            @else
            <div class="space-y-2">
                @foreach($group->matches as $match)
                <a href="{{ route('matches.show', $match) }}" class="block bg-slate-800/70 hover:bg-slate-800 rounded-lg px-3 py-2.5 transition-colors">
                    <p class="text-sm text-white">{{ $match->title ?: 'Pelada' }}</p>
                    <p class="text-xs text-slate-500">{{ $match->scheduled_at?->format('d/m/Y H:i') }} · {{ $match->status }}</p>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold mb-3">Top Ranking</h3>
            @if($rankings->isEmpty())
            <p class="text-slate-500 text-sm">Ainda sem ranking calculado.</p>
            @else
            <div class="space-y-2">
                @foreach($rankings->take(5) as $ranking)
                <div class="flex items-center justify-between text-sm bg-slate-800/70 rounded-lg px-3 py-2">
                    <span class="text-slate-200">#{{ $ranking->position }} {{ $ranking->user->name }}</span>
                    <span class="text-emerald-400 font-semibold">{{ number_format((float) $ranking->total_score, 2, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-3">Feed do Grupo</h3>
        <form method="POST" action="{{ route('groups.posts.store', $group) }}" class="space-y-3 mb-4">
            @csrf
            <textarea name="content" rows="3" maxlength="500" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white" placeholder="Compartilhe um recado com o grupo..."></textarea>
            <div class="flex justify-end">
                <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg">Publicar</button>
            </div>
        </form>

        <div class="space-y-3">
            @forelse($group->posts as $post)
            <div class="bg-slate-800/70 rounded-lg px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-100">{{ $post->content }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    @if($post->user_id === auth()->id() || $userRole === 'admin')
                    <form method="POST" action="{{ route('groups.posts.destroy', [$group, $post]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Excluir</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-slate-500 text-sm">Nenhuma publicação ainda.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
