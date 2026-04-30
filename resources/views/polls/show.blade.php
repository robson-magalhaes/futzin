@extends('layouts.app')

@section('title', $poll->title)
@section('page-title', $poll->title)
@section('breadcrumb', 'Grupos / ' . $poll->group->name . ' / Enquete')

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('groups.show', $poll->group) }}" class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2 rounded-lg">← Voltar ao Grupo</a>
    @if($userRole === 'admin' && $poll->isOpen())
    <form method="POST" action="{{ route('polls.close', $poll) }}">
        @csrf
        <button class="bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg">Encerrar Enquete</button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Info da enquete --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-white font-semibold text-lg">{{ $poll->title }}</h2>
                @if($poll->match)
                <p class="text-slate-400 text-sm mt-1">Partida: {{ $poll->match->title ?: 'Pelada' }} · {{ $poll->match->scheduled_at?->format('d/m/Y') }}</p>
                @endif
                <p class="text-xs text-slate-500 mt-1">Criada por {{ $poll->creator->name }} · {{ $poll->created_at->diffForHumans() }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $poll->isOpen() ? 'bg-emerald-900 text-emerald-300' : 'bg-slate-700 text-slate-400' }}">
                    {{ $poll->isOpen() ? 'Aberta' : 'Encerrada' }}
                </span>
                <p class="text-xs text-slate-500 mt-1">{{ $poll->type === 'mvp' ? 'Voto MVP' : 'Notas (1–10)' }}</p>
                @if($poll->closes_at)
                <p class="text-xs text-slate-500">Encerra {{ $poll->closes_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-900/50 border border-emerald-700 text-emerald-300 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-900/50 border border-red-700 text-red-300 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-900/50 border border-red-700 text-red-300 rounded-xl px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- ============ FORMULÁRIO DE VOTO ============ --}}
    @if($poll->isOpen() && !$userVote)

        {{-- MVP --}}
        @if($poll->type === 'mvp')
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold mb-4">Escolha o MVP</h3>
            <form method="POST" action="{{ route('polls.vote', $poll) }}" class="space-y-3">
                @csrf
                @foreach($members as $member)
                @if($member->id !== auth()->id())
                <label class="flex items-center gap-3 bg-slate-800/70 hover:bg-slate-800 rounded-lg px-4 py-3 cursor-pointer transition-colors">
                    <input type="radio" name="candidate_id" value="{{ $member->id }}" required class="w-4 h-4 accent-emerald-500">
                    <span class="text-slate-100 text-sm">{{ $member->name }}</span>
                    <span class="text-xs text-slate-500 ml-auto">{{ $member->pivot->role }}</span>
                </label>
                @endif
                @endforeach
                <div class="flex justify-end pt-2">
                    <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-5 py-2 rounded-lg">Votar</button>
                </div>
            </form>
        </div>
        @endif

        {{-- Rating --}}
        @if($poll->type === 'rating')
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold mb-1">Dê notas aos jogadores</h3>
            <p class="text-slate-500 text-xs mb-4">Escala de 1 a 10</p>
            <form method="POST" action="{{ route('polls.vote', $poll) }}" class="space-y-3">
                @csrf
                @foreach($members as $member)
                <div class="flex items-center gap-4 bg-slate-800/70 rounded-lg px-4 py-3">
                    <span class="text-slate-100 text-sm flex-1">{{ $member->name }}</span>
                    <div class="flex items-center gap-1">
                        @for($n = 1; $n <= 10; $n++)
                        <label class="cursor-pointer">
                            <input type="radio" name="ratings[{{ $member->id }}]" value="{{ $n }}" required
                                class="sr-only peer">
                            <span class="inline-block w-7 h-7 text-center text-xs leading-7 rounded font-semibold
                                bg-slate-700 peer-checked:bg-emerald-600 peer-checked:text-white text-slate-400
                                hover:bg-slate-600 transition-colors cursor-pointer">{{ $n }}</span>
                        </label>
                        @endfor
                    </div>
                </div>
                @endforeach
                <div class="flex justify-end pt-2">
                    <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-5 py-2 rounded-lg">Enviar Notas</button>
                </div>
            </form>
        </div>
        @endif

    @elseif($poll->isOpen() && $userVote)
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
        <p class="text-emerald-400 font-semibold">Você já votou nesta enquete.</p>
        <p class="text-slate-400 text-sm mt-1">Acompanhe o progresso parcial abaixo.</p>
    </div>
    @endif

    {{-- ============ RESULTADOS ============ --}}
    @if(!$poll->isOpen() || $userRole === 'admin' || $userVote)

        {{-- Resultados MVP --}}
        @if($poll->type === 'mvp')
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-white font-semibold">{{ $poll->isOpen() ? 'Progresso — MVP' : 'Resultado — MVP' }}</h3>
                @if($poll->isOpen())
                <span class="text-xs text-amber-400/80 bg-amber-950/40 border border-amber-700/40 px-2 py-1 rounded-full">Parcial · enquete aberta</span>
                @endif
            </div>

            @if($mvpResults->isEmpty())
            <p class="text-slate-500 text-sm">Nenhum voto registrado.</p>
            @else
            @php
                $totalVotes = $mvpResults->sum('total');
                $winner     = $mvpResults->first();
                // Empate: todos com mesmo total que o primeiro
                $tied = $mvpResults->where('total', $winner->total);
            @endphp

            {{-- Card do eleito --}}
            <div class="bg-amber-950/40 border border-amber-600/50 rounded-xl p-5 flex items-center gap-5">
                <div class="text-4xl select-none">🏆</div>
                <div class="flex-1">
                    @if($tied->count() > 1)
                    <p class="text-amber-400 text-xs font-semibold uppercase tracking-wide mb-1">Empate — MVP Eleitos</p>
                    <p class="text-white font-bold text-lg leading-snug">
                        {{ $tied->map(fn($r) => $r->candidate?->name ?? '?')->implode(' &amp; ') }}
                    </p>
                    @else
                    <p class="text-amber-400 text-xs font-semibold uppercase tracking-wide mb-1">MVP Eleito</p>
                    <p class="text-white font-bold text-xl">{{ $winner->candidate?->name ?? 'Jogador removido' }}</p>
                    @endif
                    <p class="text-amber-300/70 text-sm mt-1">
                        {{ $winner->total }} voto{{ $winner->total !== 1 ? 's' : '' }}
                        · {{ $totalVotes > 0 ? round($winner->total / $totalVotes * 100) : 0 }}% dos votos
                    </p>
                </div>
            </div>

            {{-- Tabela completa --}}
            <div>
                <p class="text-xs text-slate-400 mb-2">Todos os votos ({{ $totalVotes }} total)</p>
                <div class="space-y-2">
                    @foreach($mvpResults as $idx => $result)
                    @php $pct = $totalVotes > 0 ? round($result->total / $totalVotes * 100) : 0; @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium {{ $idx === 0 ? 'text-amber-400' : 'text-slate-200' }}">
                                {{ $idx + 1 }}. {{ $result->candidate?->name ?? 'Jogador removido' }}
                            </span>
                            <span class="text-slate-400 tabular-nums">{{ $result->total }} voto{{ $result->total !== 1 ? 's' : '' }} &nbsp;<span class="text-slate-500">({{ $pct }}%)</span></span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all {{ $idx === 0 ? 'bg-amber-500' : 'bg-slate-600' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Resultados Rating --}}
        @if($poll->type === 'rating')
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white font-semibold">{{ $poll->isOpen() ? 'Progresso — Notas' : 'Resultado — Notas' }}</h3>
                @if($poll->isOpen())
                <span class="text-xs text-amber-400/80 bg-amber-950/40 border border-amber-700/40 px-2 py-1 rounded-full">Parcial · enquete aberta</span>
                @endif
            </div>
            @if($ratingResults->isEmpty())
            <p class="text-slate-500 text-sm">Nenhuma nota registrada.</p>
            @else
            <div class="space-y-2">
                @foreach($ratingResults as $idx => $result)
                <div class="flex items-center gap-4 bg-slate-800/70 rounded-lg px-4 py-3">
                    <span class="text-slate-400 text-xs w-4">{{ $idx + 1 }}.</span>
                    <span class="text-slate-100 text-sm flex-1">{{ $result->candidate?->name ?? 'Jogador removido' }}</span>
                    <div class="text-right">
                        <span class="text-emerald-400 font-bold text-lg">{{ number_format($result->avg_rating, 1) }}</span>
                        <span class="text-slate-500 text-xs"> / 10</span>
                        <p class="text-xs text-slate-500">{{ $result->votes_count }} avaliação{{ $result->votes_count !== 1 ? 'ões' : '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

    @endif

</div>
@endsection
