@extends('layouts.app')

@section('title', $group->name)
@section('page-title', $group->name)
@section('breadcrumb', 'Grupos / ' . $group->name)

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('matches.create', ['group_id' => $group->id]) }}"
       class="w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center"
       title="Agendar Partida" aria-label="Agendar Partida">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </a>
    @if($userRole === 'admin')
    <a href="{{ route('groups.edit', $group) }}"
       class="w-10 h-10 rounded-lg bg-slate-700 hover:bg-slate-600 text-white flex items-center justify-center"
       title="Editar Grupo" aria-label="Editar Grupo">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    </a>
    <a href="{{ route('groups.members.manage', $group) }}"
       class="w-10 h-10 rounded-lg bg-amber-700 hover:bg-amber-600 text-white flex items-center justify-center"
       title="Bloquear e Desbloquear Jogadores" aria-label="Bloquear e Desbloquear Jogadores">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4s-3 1.567-3 3.5 1.343 3.5 3 3.5zm0 0v2m0 0l-4 4m4-4l4 4"/></svg>
    </a>
    <button type="button"
       class="w-10 h-10 rounded-lg bg-purple-700 hover:bg-purple-600 text-white flex items-center justify-center"
       title="Historico de Enquetes" aria-label="Historico de Enquetes"
       onclick="openPollHistoryModal()">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </button>
    @endif
    @if(auth()->id() !== $group->user_id)
    <form method="POST" action="{{ route('groups.leave', $group) }}" onsubmit="return confirm('Deseja realmente sair deste grupo?');">
        @csrf
        <button class="w-10 h-10 rounded-lg bg-red-700 hover:bg-red-600 text-white flex items-center justify-center" title="Sair do Grupo" aria-label="Sair do Grupo">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
        </button>
    </form>
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
                <div class="flex items-center justify-between gap-3 text-sm bg-slate-800/60 rounded-lg px-3 py-2">
                    <div class="min-w-0">
                        <p class="text-slate-200 truncate">{{ $member->name }}</p>
                        <span class="text-xs {{ $member->pivot->role === 'admin' ? 'text-amber-400' : 'text-slate-500' }}">{{ $member->pivot->role }}</span>
                    </div>
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

    {{-- ============ ENQUETES ============ --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Enquetes Abertas e da Partida</h3>
        </div>

        {{-- Formulário de criação (admin only) --}}
        @if($userRole === 'admin')
        <details class="mb-4">
            <summary class="cursor-pointer text-sm text-emerald-400 hover:text-emerald-300 select-none">+ Nova enquete</summary>
            <form method="POST" action="{{ route('polls.store', $group) }}" class="mt-3 space-y-3 bg-slate-800/60 rounded-xl p-4">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Título *</label>
                    <input type="text" name="title" required maxlength="255"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm"
                        placeholder="Ex: MVP da última pelada">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Tipo *</label>
                        <select name="type" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                            <option value="mvp">Votação MVP</option>
                            <option value="rating">Notas para os jogadores (1–10)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Partida vinculada (opcional)</label>
                        <select name="match_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                            <option value="">— Nenhuma —</option>
                            @foreach($group->matches as $m)
                            <option value="{{ $m->id }}">{{ $m->title ?: 'Pelada' }} · {{ $m->scheduled_at?->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Data de encerramento (opcional)</label>
                    <input type="datetime-local" name="closes_at"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
                </div>
                <div class="flex justify-end">
                    <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg">Criar Enquete</button>
                </div>
            </form>
        </details>
        @endif

        {{-- Lista de enquetes --}}
        @if($visiblePolls->isEmpty())
        <p class="text-slate-500 text-sm">Nenhuma enquete aberta ou vinculada a partida.</p>
        @else
        <div class="space-y-2">
            @foreach($visiblePolls as $poll)
            <a href="{{ route('polls.show', $poll) }}" class="flex items-center justify-between bg-slate-800/70 hover:bg-slate-800 rounded-lg px-4 py-3 transition-colors">
                <div>
                    <p class="text-sm text-white font-medium">{{ $poll->title }}</p>
                    <p class="text-xs text-slate-500">{{ $poll->type === 'mvp' ? 'MVP' : 'Notas' }} · {{ $poll->creator->name }} · {{ $poll->match?->title ?: 'Sem partida' }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $poll->isOpen() ? 'bg-emerald-900 text-emerald-400' : 'bg-slate-700 text-slate-400' }}">
                    {{ $poll->isOpen() ? 'Aberta' : 'Encerrada' }}
                </span>
            </a>
            @endforeach
        </div>
        @endif
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

@if($userRole === 'admin')
<div id="poll-history-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70" onclick="closePollHistoryModal()"></div>
    <div class="relative max-w-2xl mx-auto mt-20 bg-slate-900 border border-slate-800 rounded-xl p-5 max-h-[75vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Historico de Enquetes</h3>
            <button class="text-slate-400 hover:text-white" onclick="closePollHistoryModal()">Fechar</button>
        </div>

        @if($pollHistory->isEmpty())
        <p class="text-slate-500 text-sm">Nenhuma enquete encerrada no historico.</p>
        @else
        <div class="space-y-2">
            @foreach($pollHistory as $poll)
            <a href="{{ route('polls.show', $poll) }}" class="flex items-center justify-between bg-slate-800/70 hover:bg-slate-800 rounded-lg px-4 py-3 transition-colors">
                <div>
                    <p class="text-sm text-white font-medium">{{ $poll->title }}</p>
                    <p class="text-xs text-slate-500">{{ $poll->type === 'mvp' ? 'MVP' : 'Notas' }} · {{ $poll->creator->name }} · {{ $poll->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-700 text-slate-300">Encerrada</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
function openPollHistoryModal() {
    document.getElementById('poll-history-modal').classList.remove('hidden');
}

function closePollHistoryModal() {
    document.getElementById('poll-history-modal').classList.add('hidden');
}
</script>
@endif
@endsection
