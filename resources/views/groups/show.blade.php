@extends('layouts.app')

@section('title', $group->name)
@section('page-title', $group->name)
@section('breadcrumb', 'Grupos / ' . $group->name)

@section('header-actions')
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-slate-400 text-sm">{{ $group->description ?: 'Sem descrição cadastrada.' }}</p>
                    <p class="text-xs text-slate-500 mt-2">
                        {{ $group->fee_type === 'daily' ? 'Valor diário' : 'Mensalidade' }}:
                        R$ {{ number_format((float) $group->monthly_fee, 2, ',', '.') }}
                    </p>
                    @if($group->schedule_type === 'weekly' && $group->weekly_day !== null)
                    @php
                        $days = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
                        $weekLabel = $days[$group->weekly_day] ?? '';
                        $timeLabel = $group->weekly_time ? substr($group->weekly_time, 0, 5) : '';
                    @endphp
                    <p class="text-xs text-blue-400 mt-1">
                        Rodada semanal: {{ $weekLabel }}{{ $timeLabel ? ' às ' . $timeLabel : '' }}
                    </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Código de entrada</p>
                    <p class="font-mono text-emerald-400 tracking-wider text-lg">{{ $group->join_code }}</p>
                </div>
            </div>

            {{-- Linha de ações rápidas --}}
            <div class="mt-4 flex gap-3 flex-wrap items-center">
                {{-- Confirmar / Cancelar Presença --}}
                @php
                    $canConfirm = $group->schedule_type === 'scheduled' || $currentOpenMatch !== null;
                    $lockPassed = false;
                    if ($userPresenceConfirmed && $group->confirmation_lock_at) {
                        $lockDay = $group->weekly_day;
                        if ($lockDay === null || now()->dayOfWeek === $lockDay) {
                            try {
                                $lockTime = \Carbon\Carbon::today()->setTimeFromTimeString($group->confirmation_lock_at);
                                $lockPassed = now()->greaterThan($lockTime);
                            } catch (\Exception $e) {}
                        }
                    }
                @endphp
                @if($canConfirm || $userPresenceConfirmed)
                <form method="POST" action="{{ route('groups.confirm-presence', $group) }}">@csrf
                    @if($userPresenceConfirmed && $lockPassed)
                    {{-- Confirmado e bloqueado: não pode cancelar --}}
                    <button type="button" disabled
                        class="flex items-center gap-2 px-3 h-10 rounded-lg text-sm font-medium bg-emerald-900 text-emerald-400 opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmado
                    </button>
                    @elseif($userPresenceConfirmed)
                    {{-- Confirmado: toque para cancelar --}}
                    <button type="submit"
                        class="flex items-center gap-2 px-3 h-10 rounded-lg text-sm font-medium bg-emerald-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmado
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @else
                    {{-- Não confirmado --}}
                    <button type="submit"
                        class="flex items-center gap-2 px-3 h-10 rounded-lg text-sm font-medium bg-slate-700 text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Confirmar presença
                    </button>
                    @endif
                </form>
                @endif

                {{-- Ranking --}}
                <a href="{{ route('rankings.show', $group) }}"
                   class="w-10 h-10 rounded-lg bg-slate-800 hover:bg-slate-700 text-white flex items-center justify-center"
                   title="Ver ranking completo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M8 13l3-3 2 2 4-4"/>
                    </svg>
                </a>

                {{-- Marcar como pago (mensalidade pendente) --}}
                @if($userPayout)
                <form method="POST" action="{{ route('payouts.mark-paid', $userPayout) }}">@csrf
                    <button class="flex items-center gap-2 px-3 h-10 rounded-lg bg-amber-700 text-white text-sm font-medium"
                        title="Informar pagamento de R$ {{ number_format((float) $userPayout->amount, 2, ',', '.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pagar mensalidade
                    </button>
                </form>
                @endif

                {{-- Confirmar pagamento de diária --}}
                @if($group->fee_type === 'daily' && ($canConfirm || $userPresenceConfirmed) && !$userDailyPaidToday)
                <form method="POST" action="{{ route('groups.report-daily-payment', $group) }}">@csrf
                    <button class="flex items-center gap-2 px-3 h-10 rounded-lg bg-emerald-900 hover:bg-emerald-800 text-emerald-300 text-sm font-medium border border-emerald-700"
                        title="Confirmar que pagou a diária de R$ {{ number_format((float) $group->monthly_fee, 2, ',', '.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Paguei a diária
                    </button>
                </form>
                @elseif($group->fee_type === 'daily' && $userDailyPaidToday)
                <span class="flex items-center gap-2 px-3 h-10 rounded-lg bg-slate-800 text-slate-400 text-sm border border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Diária paga
                </span>
                @endif

                {{-- Botão único de configurações (abre modal) --}}
                <button type="button" onclick="openConfigModal()"
                    class="w-10 h-10 rounded-lg bg-slate-700 hover:bg-slate-600 text-white flex items-center justify-center ml-auto"
                    title="Configurações do grupo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Alerta de rodada semanal para admin --}}
            @if($group->schedule_type === 'weekly' && $userRole === 'admin')
            <div class="mt-3 pt-3 border-t border-slate-800">
                @if($currentOpenMatch)
                <div class="flex items-center justify-between bg-blue-950/40 border border-blue-800/50 rounded-lg px-4 py-3">
                    <div>
                        <p class="text-sm text-blue-300 font-medium">Rodada ativa: {{ $currentOpenMatch->title }}</p>
                        <p class="text-xs text-blue-400/70">{{ $currentOpenMatch->scheduled_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <a href="{{ route('matches.show', $currentOpenMatch) }}"
                       class="text-xs bg-blue-700 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg">Ver partida</a>
                </div>
                @else
                <form method="POST" action="{{ route('groups.start-round', $group) }}">@csrf
                    <button class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Iniciar Rodada da Semana
                    </button>
                </form>
                @endif
            </div>
            @elseif($group->schedule_type === 'weekly' && $currentOpenMatch)
            <div class="mt-3 pt-3 border-t border-slate-800">
                <div class="flex items-center gap-2 bg-blue-950/40 border border-blue-800/50 rounded-lg px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-blue-300">Rodada ativa: {{ $currentOpenMatch->scheduled_at?->format('d/m H:i') }}</p>
                </div>
            </div>
            @endif
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
                    @if($member->pivot->presence_confirmed)
                    <span class="text-emerald-400 shrink-0" title="Confirmado">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    @endif
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

{{-- ===== MODAL DE CONFIGURAÇÕES DO GRUPO ===== --}}
<div id="config-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70" onclick="closeConfigModal()"></div>
    <div class="relative max-w-sm mx-auto mt-24 bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Ações do Grupo</h3>
            <button class="text-slate-400 hover:text-white text-sm" onclick="closeConfigModal()">Fechar</button>
        </div>

        <div class="space-y-2">
            {{-- Agendar Partida / Ver partida atual --}}
            @if($group->schedule_type === 'scheduled')
            <a href="{{ route('matches.create', ['group_id' => $group->id]) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Agendar Partida</p>
                </div>
            </a>
            @elseif($currentOpenMatch)
            <a href="{{ route('matches.show', $currentOpenMatch) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Ver Rodada Atual</p>
                    <p class="text-xs text-slate-400">{{ $currentOpenMatch->scheduled_at?->format('d/m/Y H:i') }}</p>
                </div>
            </a>
            @endif

            {{-- Pagamentos (para todos) --}}
            <a href="{{ route('payouts.group', $group) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Pagamentos do Grupo</p>
                </div>
            </a>

            @if($userRole === 'admin')
            {{-- Editar Grupo --}}
            <a href="{{ route('groups.edit', $group) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Configurações do Grupo</p>
                    <p class="text-xs text-slate-400">Nome, valor, agendamento, ranking</p>
                </div>
            </a>

            {{-- Gerenciar Membros --}}
            <a href="{{ route('groups.members.manage', $group) }}"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Gerenciar Membros</p>
                    <p class="text-xs text-slate-400">Bloquear e desbloquear jogadores</p>
                </div>
            </a>

            {{-- Histórico de Enquetes --}}
            <button type="button" onclick="closeConfigModal(); openPollHistoryModal();"
               class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors text-left">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium">Histórico de Enquetes</p>
                    <p class="text-xs text-slate-400">Enquetes encerradas</p>
                </div>
            </button>
            @endif

            {{-- Sair do Grupo --}}
            @if(auth()->id() !== $group->user_id)
            <form method="POST" action="{{ route('groups.leave', $group) }}" onsubmit="return confirm('Deseja realmente sair deste grupo?');">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg bg-red-950/40 hover:bg-red-950/70 text-red-400 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                    </svg>
                    <p class="text-sm font-medium">Sair do Grupo</p>
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- ===== MODAL HISTÓRICO DE ENQUETES (admin) ===== --}}
@if($userRole === 'admin')
<div id="poll-history-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70" onclick="closePollHistoryModal()"></div>
    <div class="relative max-w-2xl mx-auto mt-20 bg-slate-900 border border-slate-800 rounded-xl p-5 max-h-[75vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Histórico de Enquetes</h3>
            <button class="text-slate-400 hover:text-white" onclick="closePollHistoryModal()">Fechar</button>
        </div>

        @if($pollHistory->isEmpty())
        <p class="text-slate-500 text-sm">Nenhuma enquete encerrada no histórico.</p>
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
@endif

<script>
function openConfigModal() {
    document.getElementById('config-modal').classList.remove('hidden');
}
function closeConfigModal() {
    document.getElementById('config-modal').classList.add('hidden');
}
function openPollHistoryModal() {
    const el = document.getElementById('poll-history-modal');
    if (el) el.classList.remove('hidden');
}
function closePollHistoryModal() {
    const el = document.getElementById('poll-history-modal');
    if (el) el.classList.add('hidden');
}
</script>
@endsection
