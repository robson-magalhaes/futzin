@extends('layouts.app')

@section('title', 'Nova Partida')
@section('page-title', 'Agendar Partida')
@section('breadcrumb', 'Partidas / Agendar')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('matches.store') }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Grupo</label>
            <select name="group_id" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
                <option value="">Selecione...</option>
                @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ (string) old('group_id', $selectedGroupId) === (string) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Título</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white" placeholder="Pelada de sábado">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Data e hora</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Local</label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white" placeholder="Arena Centro">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-medium">Agendar</button>
        </div>
    </form>
</div>
@endsection
