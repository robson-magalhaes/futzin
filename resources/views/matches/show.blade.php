@extends('layouts.app')

@section('title', $match->title ?: 'Partida')
@section('page-title', $match->title ?: 'Partida')
@section('breadcrumb', 'Partidas / Detalhes')

@section('header-actions')
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-slate-500">Grupo</p>
                <p class="text-white">{{ $match->group->name }}</p>
            </div>
            <div>
                <p class="text-slate-500">Data</p>
                <p class="text-white">{{ $match->scheduled_at?->format('d/m/Y H:i') ?: '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Local</p>
                <p class="text-white">{{ $match->location ?: '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Status</p>
                <p class="{{ $match->status === 'finished' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $match->status === 'finished' ? 'Finalizada' : 'Agendada' }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-800 flex gap-3">
            <a href="{{ route('matches.teams.form', $match) }}" class="flex-1 text-center bg-slate-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
                {{ $userRole === 'admin' ? 'Gerar Times' : 'Ver Times' }}
            </a>
            @if($userRole === 'admin' && $match->status !== 'finished')
            <a href="{{ route('matches.finish.form', $match) }}" class="flex-1 text-center bg-emerald-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg">Finalizar Partida</a>
            @endif
        </div>
    </div>

    @if($match->teams->isNotEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-3">Placar</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($match->teams as $team)
            <div class="bg-slate-800/70 rounded-lg p-4 flex items-center justify-between">
                <span class="text-slate-200">{{ $team->name }}</span>
                <span class="text-xl font-bold text-white">{{ $team->goals }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-3">Jogadores</h3>
        @if($match->players->isEmpty())
        <p class="text-slate-500 text-sm">Sem estatísticas registradas ainda.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-left text-slate-500 border-b border-slate-800">
                    <th class="py-2 pr-3">Jogador</th>
                    <th class="py-2 pr-3">Gols</th>
                    <th class="py-2 pr-3">Assists</th>
                    <th class="py-2 pr-3">MVP</th>
                    <th class="py-2 pr-3">Time</th>
                </tr>
                </thead>
                <tbody>
                @foreach($match->players as $player)
                <tr class="border-b border-slate-800/60">
                    <td class="py-2 pr-3 text-slate-200">{{ $player->name }}</td>
                    <td class="py-2 pr-3 text-slate-300">{{ $player->pivot->goals }}</td>
                    <td class="py-2 pr-3 text-slate-300">{{ $player->pivot->assists }}</td>
                    <td class="py-2 pr-3 text-slate-300">{{ $player->pivot->is_mvp ? 'Sim' : 'Não' }}</td>
                    <td class="py-2 pr-3 text-slate-300">{{ optional($match->teams->firstWhere('id', $player->pivot->team_id))->name ?: '-' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
