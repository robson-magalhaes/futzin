@extends('layouts.app')

@section('title', 'Novo Grupo')
@section('page-title', 'Criar Grupo')
@section('breadcrumb', 'Grupos / Criar')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('groups.store') }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Nome do grupo</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500"
                   placeholder="Futebol da Quarta">
        </div>

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Descrição</label>
            <textarea name="description" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500" placeholder="Regras e observações do grupo">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Mensalidade (R$)</label>
            <input type="number" step="0.01" min="0" name="monthly_fee" value="{{ old('monthly_fee', '0.00') }}" required
                   class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('groups.index') }}" class="text-slate-400 hover:text-white text-sm">Cancelar</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-medium">Criar Grupo</button>
        </div>
    </form>
</div>
@endsection
