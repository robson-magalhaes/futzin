@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('header-actions')
<a href="{{ route('groups.create') }}"
   class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-lg shadow-emerald-900/20">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Novo Grupo
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-emerald-900/30 to-slate-900/50 border border-emerald-800/30 rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-white">
            Olá, {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </h2>
        <p class="text-slate-400 text-sm mt-1">
            {{ now()->locale('pt_BR')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            @if(!auth()->user()->activeSubscription())
            · <a href="{{ route('subscription.index') }}" class="text-emerald-400 hover:text-emerald-300 transition-colors">Ative um plano para desbloquear todos os recursos →</a>
            @endif
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400">Grupos</p>
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $stats['groups'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">grupos ativos</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400">Próximas</p>
                <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $stats['upcoming_matches'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">partidas agendadas</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400">Gols</p>
                <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                    <span class="text-sm">⚽</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $stats['total_goals'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">total na carreira</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400">MVPs</p>
                <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center">
                    <span class="text-sm">🏆</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $stats['total_mvps'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">vezes eleito MVP</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Upcoming matches --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                <h3 class="font-semibold text-white">Próximas Partidas</h3>
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>

            @if($upcomingMatches->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-slate-500">
                <span class="text-3xl mb-2">📅</span>
                <p class="text-sm">Nenhuma partida agendada</p>
            </div>
            @else
            <div class="divide-y divide-slate-800">
                @foreach($upcomingMatches as $match)
                <a href="{{ route('matches.show', $match) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-800/50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex flex-col items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-blue-400 leading-none">{{ $match->scheduled_at->format('d') }}</span>
                        <span class="text-[10px] text-blue-500/70 leading-none mt-0.5">{{ strtoupper($match->scheduled_at->locale('pt_BR')->isoFormat('MMM')) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $match->title ?? 'Pelada' }}</p>
                        <p class="text-xs text-slate-500">{{ $match->group->name }} · {{ $match->scheduled_at->format('H:i') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- My groups --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                <h3 class="font-semibold text-white">Meus Grupos</h3>
                <a href="{{ route('groups.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors">Ver todos →</a>
            </div>

            @if($groups->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-slate-500">
                <span class="text-3xl mb-2">👥</span>
                <p class="text-sm">Você não está em nenhum grupo</p>
                <a href="{{ route('groups.create') }}" class="mt-3 text-xs text-emerald-400 hover:text-emerald-300 transition-colors">Criar primeiro grupo →</a>
            </div>
            @else
            <div class="divide-y divide-slate-800">
                @foreach($groups->take(5) as $group)
                <a href="{{ route('groups.show', $group) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-800/50 transition-colors group">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-xl flex items-center justify-center shrink-0 text-sm font-bold shadow-lg shadow-emerald-900/30">
                        {{ strtoupper(substr($group->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $group->name }}</p>
                        <p class="text-xs text-slate-500">{{ $group->members_count }} membros</p>
                    </div>
                    @php
                        $role = $group->pivot->role ?? 'player';
                    @endphp
                    @if($role === 'admin')
                    <span class="shrink-0 text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded-full">Admin</span>
                    @endif
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Recent matches --}}
    @if($recentMatches->isNotEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-white">Partidas Recentes</h3>
        </div>
        <div class="divide-y divide-slate-800">
            @foreach($recentMatches as $match)
            <a href="{{ route('matches.show', $match) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-800/50 transition-colors group">
                <div class="w-10 h-10 bg-slate-700/50 rounded-xl flex items-center justify-center shrink-0">
                    <span class="text-base">⚽</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $match->title ?? 'Pelada' }}</p>
                    <p class="text-xs text-slate-500">{{ $match->group->name }} · {{ $match->scheduled_at->locale('pt_BR')->isoFormat('D [de] MMM') }}</p>
                </div>
                <span class="shrink-0 text-xs bg-slate-700 text-slate-400 px-2 py-0.5 rounded-full">Finalizada</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
