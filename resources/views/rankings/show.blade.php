@extends('layouts.app')

@section('title', 'Ranking')
@section('page-title', 'Ranking - ' . $group->name)
@section('breadcrumb', 'Grupos / Ranking')

@section('content')
<div class="space-y-5">
    @if($myRanking)
    <div class="bg-emerald-900/20 border border-emerald-700/40 rounded-xl p-4 text-sm">
        <p class="text-emerald-300">Sua posição: <strong>#{{ $myRanking->position }}</strong> com {{ number_format((float) $myRanking->total_score, 2, ',', '.') }} pontos.</p>
    </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-left text-slate-500 border-b border-slate-800">
                    <th class="px-4 py-3">Pos.</th>
                    <th class="px-4 py-3">Jogador</th>
                    <th class="px-4 py-3">Partidas</th>
                    <th class="px-4 py-3">Vitórias</th>
                    <th class="px-4 py-3">Votos MVP</th>
                    <th class="px-4 py-3">Média</th>
                    <th class="px-4 py-3">Penalidades</th>
                    <th class="px-4 py-3">Pontos</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rankings as $ranking)
                <tr class="border-b border-slate-800/70 {{ $ranking->user_id === auth()->id() ? 'bg-emerald-600/10' : '' }}">
                    <td class="px-4 py-3 text-slate-300">#{{ $ranking->position }}</td>
                    <td class="px-4 py-3 text-white">{{ $ranking->user->name }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ $ranking->matches_played }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ $ranking->wins }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ $ranking->mvp_count }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ number_format((float) $ranking->average_rating, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 {{ (float) $ranking->points_penalty < 0 ? 'text-red-400' : 'text-slate-300' }}">{{ number_format((float) $ranking->points_penalty, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-emerald-400 font-semibold">{{ number_format((float) $ranking->total_score, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-500">Sem dados de ranking para este grupo.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
