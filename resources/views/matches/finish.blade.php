@extends('layouts.app')

@section('title', 'Finalizar Partida')
@section('page-title', 'Finalizar Partida')
@section('breadcrumb', 'Partidas / Finalizar')

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <p class="text-sm text-slate-400">{{ $match->title ?: 'Pelada' }} · {{ $match->scheduled_at?->format('d/m/Y H:i') }}</p>
    </div>

    <form method="POST" action="{{ route('matches.finish', $match) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
        @csrf

        @php
        $teamA = $teams[0] ?? null;
        $teamB = $teams[1] ?? null;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">{{ $teamA?->name ?? 'Time A' }}</label>
                <input type="number" name="team_a_goals" min="0" value="{{ old('team_a_goals', $teamA?->goals ?? 0) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">{{ $teamB?->name ?? 'Time B' }}</label>
                <input type="number" name="team_b_goals" min="0" value="{{ old('team_b_goals', $teamB?->goals ?? 0) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-3">Penalidades da Partida</h3>
            <p class="text-slate-500 text-xs mb-3">Apenas registre cartões. O ranking usa votação das enquetes, vitórias e penalidades.</p>
            <div class="space-y-3">
                @foreach($members as $member)
                <div class="bg-slate-800/60 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                    <p class="text-sm text-slate-100">{{ $member->name }}</p>
                    <select name="penalties[{{ $member->id }}]" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white">
                        <option value="none">Sem penalidade</option>
                        <option value="yellow_card">Cartão amarelo (-1)</option>
                        <option value="red_card">Cartão vermelho (-3)</option>
                    </select>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-medium">Finalizar e Atualizar Ranking</button>
        </div>
    </form>
</div>
@endsection
