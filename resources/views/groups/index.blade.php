@extends('layouts.app')

@section('title', 'Meus Grupos')
@section('page-title', 'Meus Grupos')

@section('header-actions')
<a href="{{ route('groups.create') }}"
   class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center"
   title="Novo Grupo" aria-label="Novo Grupo">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="text-white font-semibold mb-3">Entrar com código</h2>
        <form method="POST" action="{{ route('groups.join') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="join_code" maxlength="6" required placeholder="Ex: ABC123"
                   class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white uppercase tracking-widest placeholder-slate-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg font-medium">Entrar</button>
        </form>
    </div>

    @if($groups->isEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center">
        <p class="text-slate-400">Você ainda não participa de nenhum grupo.</p>
        <a href="{{ route('groups.create') }}" class="inline-block mt-4 text-emerald-400 hover:text-emerald-300">Criar primeiro grupo</a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($groups as $group)
        <a href="{{ route('groups.show', $group) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-5 hover:border-emerald-600/40 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-white font-semibold">{{ $group->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Dono: {{ $group->owner->name }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $group->pivot->role === 'admin' ? 'bg-amber-500/10 text-amber-400' : 'bg-slate-700 text-slate-300' }}">
                    {{ $group->pivot->role === 'admin' ? 'Admin' : 'Jogador' }}
                </span>
            </div>
            <div class="mt-4 text-sm text-slate-400 space-y-1">
                <p>{{ $group->members_count }} membros</p>
                <p>Mensalidade: R$ {{ number_format((float) $group->monthly_fee, 2, ',', '.') }}</p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
