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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Nome Time A</label>
                <input type="text" name="team_a_name" value="{{ old('team_a_name', 'Time A') }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Gols Time A</label>
                <input type="number" name="team_a_goals" min="0" value="{{ old('team_a_goals', 0) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Nome Time B</label>
                <input type="text" name="team_b_name" value="{{ old('team_b_name', 'Time B') }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Gols Time B</label>
                <input type="number" name="team_b_goals" min="0" value="{{ old('team_b_goals', 0) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-3">Estatísticas dos Jogadores</h3>
            <div class="space-y-3">
                @foreach($members as $index => $member)
                <div class="bg-slate-800/60 rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    <input type="hidden" name="players[{{ $index }}][user_id]" value="{{ $member->id }}">

                    <div class="md:col-span-2">
                        <label class="block text-xs text-slate-400 mb-1">Jogador</label>
                        <p class="text-sm text-slate-100">{{ $member->name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Time</label>
                        <select name="players[{{ $index }}][team]" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white">
                            <option value="a">A</option>
                            <option value="b">B</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Gols</label>
                        <input type="number" min="0" name="players[{{ $index }}][goals]" value="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white">
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Assist.</label>
                        <input type="number" min="0" name="players[{{ $index }}][assists]" value="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white">
                    </div>

                    <div class="flex gap-3 md:justify-end">
                        <label class="inline-flex items-center gap-1.5 text-xs text-slate-300"><input type="checkbox" name="players[{{ $index }}][is_mvp]"> MVP</label>
                        <label class="inline-flex items-center gap-1.5 text-xs text-slate-300"><input type="checkbox" name="players[{{ $index }}][yellow_card]"> Am</label>
                        <label class="inline-flex items-center gap-1.5 text-xs text-slate-300"><input type="checkbox" name="players[{{ $index }}][red_card]"> Vm</label>
                    </div>
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
