@extends('layouts.app')

@section('title', ($userRole === 'admin' ? 'Gerar Times' : 'Times da Partida') . ' — ' . ($match->title ?: 'Pelada'))
@section('page-title', $userRole === 'admin' ? 'Gerar Times' : 'Times da Partida')
@section('breadcrumb', 'Partidas / ' . ($match->title ?: 'Pelada') . ' / Times')

@section('header-actions')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if($userRole === 'admin')

    {{-- Formulário de geração --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="text-white font-semibold text-lg mb-4">Configurar e Gerar Times</h2>

        <form method="POST" action="{{ route('matches.teams.generate', $match) }}" id="teams-form" class="space-y-5">
            @csrf

            {{-- Nomes dos times --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nome — Time A *</label>
                    <input type="text" name="team_a_name" required value="{{ old('team_a_name', 'Time A') }}"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nome — Time B *</label>
                    <input type="text" name="team_b_name" required value="{{ old('team_b_name', 'Time B') }}"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                </div>
            </div>

            {{-- Método --}}
            <div>
                <p class="text-xs text-slate-400 mb-2">Método de divisão *</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="method-options">
                    @foreach(['random_general' => ['label' => 'Aleatório Geral', 'desc' => 'Sorteia todos os jogadores sem critério'], 'random_by_position' => ['label' => 'Aleatório por Posição', 'desc' => 'Distribui equilibrando as posições'], 'manual' => ['label' => 'Manual', 'desc' => 'Você define cada time']] as $value => $info)
                    <label class="cursor-pointer">
                        <input type="radio" name="method" value="{{ $value }}" class="sr-only peer method-radio"
                            {{ old('method', 'random_general') === $value ? 'checked' : '' }}>
                        <div class="border border-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-950/30 rounded-xl p-4 transition-colors">
                            <p class="text-sm font-medium text-white peer-checked:text-emerald-300">{{ $info['label'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $info['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Seleção manual (oculto por padrão) --}}
            <div id="manual-section" class="{{ old('method') === 'manual' ? '' : 'hidden' }} space-y-4">
                <p class="text-xs text-slate-400">Selecione os jogadores de cada time. Um jogador pode estar em apenas um time.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-white font-medium mb-2">Time A</p>
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($members as $member)
                            <label class="flex items-center gap-3 bg-slate-800/70 hover:bg-slate-800 rounded-lg px-3 py-2 cursor-pointer transition-colors">
                                <input type="checkbox" name="team_a_players[]" value="{{ $member->id }}"
                                    class="w-4 h-4 accent-emerald-500 player-a-check"
                                    data-player="{{ $member->id }}"
                                    {{ in_array($member->id, old('team_a_players', [])) ? 'checked' : '' }}>
                                <div>
                                    <span class="text-slate-100 text-sm">{{ $member->name }}</span>
                                    @if($member->position)
                                    <span class="text-xs text-slate-500 ml-1">({{ $member->position }})</span>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-white font-medium mb-2">Time B</p>
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($members as $member)
                            <label class="flex items-center gap-3 bg-slate-800/70 hover:bg-slate-800 rounded-lg px-3 py-2 cursor-pointer transition-colors">
                                <input type="checkbox" name="team_b_players[]" value="{{ $member->id }}"
                                    class="w-4 h-4 accent-blue-500 player-b-check"
                                    data-player="{{ $member->id }}"
                                    {{ in_array($member->id, old('team_b_players', [])) ? 'checked' : '' }}>
                                <div>
                                    <span class="text-slate-100 text-sm">{{ $member->name }}</span>
                                    @if($member->position)
                                    <span class="text-xs text-slate-500 ml-1">({{ $member->position }})</span>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-6 py-2.5 rounded-lg">Gerar Times</button>
            </div>
        </form>
        @endif {{-- admin --}}
    </div>

    {{-- Times atuais --}}
    @if($teams->isNotEmpty())
    <div class="space-y-2 mb-2">
        <h2 class="text-white font-semibold text-lg">Composição dos Times</h2>
        @if($userRole !== 'admin')
        <p class="text-slate-500 text-xs">Times definidos pelo administrador do grupo.</p>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($teams as $team)
        @php $colors = $loop->first ? ['dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-900 text-emerald-300', 'border' => 'border-emerald-800/50'] : ['dot' => 'bg-blue-500', 'badge' => 'bg-blue-900 text-blue-300', 'border' => 'border-blue-800/50']; @endphp
        <div class="bg-slate-900 border {{ $colors['border'] }} rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-3 h-3 rounded-full {{ $colors['dot'] }}"></span>
                <h3 class="text-white font-semibold">{{ $team->name }}</h3>
                <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ $colors['badge'] }}">{{ $team->players->count() }} jogador{{ $team->players->count() !== 1 ? 'es' : '' }}</span>
            </div>
            @if($team->players->isEmpty())
            <p class="text-slate-500 text-sm">Nenhum jogador alocado.</p>
            @else
            <ul class="space-y-2">
                @foreach($team->players as $pm)
                <li class="flex items-center gap-2 text-sm bg-slate-800/60 rounded-lg px-3 py-2 {{ $pm->user_id === auth()->id() ? 'ring-1 ring-emerald-500/50' : '' }}">
                    <span class="w-2 h-2 rounded-full {{ $colors['dot'] }} shrink-0"></span>
                    <span class="text-slate-100 flex-1">{{ $pm->user?->name ?? '—' }}{{ $pm->user_id === auth()->id() ? ' (você)' : '' }}</span>
                    @if($pm->user?->position)
                    <span class="text-xs text-slate-500">{{ $pm->user->position }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 text-center">
        <p class="text-slate-500 text-sm">Nenhum time gerado ainda.</p>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('.method-radio');
    const manualSection = document.getElementById('manual-section');

    function updateVisibility() {
        const checked = document.querySelector('.method-radio:checked');
        manualSection.classList.toggle('hidden', !checked || checked.value !== 'manual');
    }

    radios.forEach(r => r.addEventListener('change', updateVisibility));
    updateVisibility();
});
</script>
@endsection
