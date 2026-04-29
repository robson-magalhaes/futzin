@extends('layouts.app')

@section('title', 'Editar Grupo')
@section('page-title', 'Editar Grupo')
@section('breadcrumb', 'Grupos / Editar')

@section('content')
<div class="max-w-2xl space-y-5">
    <form method="POST" action="{{ route('groups.update', $group) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Nome do grupo</label>
            <input type="text" name="name" value="{{ old('name', $group->name) }}" required
                   class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
        </div>

        <div>
            <label class="block text-sm text-slate-300 mb-1.5">Descrição</label>
            <textarea name="description" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">{{ old('description', $group->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Mensalidade (R$)</label>
                <input type="number" step="0.01" min="0" name="monthly_fee" value="{{ old('monthly_fee', $group->monthly_fee) }}" required
                       class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1.5">Status</label>
                <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white">
                    <option value="active" {{ old('status', $group->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ old('status', $group->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('groups.show', $group) }}" class="text-slate-400 hover:text-white text-sm">Cancelar</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-medium">Salvar</button>
        </div>
    </form>

    <form method="POST" action="{{ route('groups.destroy', $group) }}" class="bg-red-950/20 border border-red-800/40 rounded-xl p-4">
        @csrf
        @method('DELETE')
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-red-300 text-sm font-medium">Excluir grupo</p>
                <p class="text-red-400/80 text-xs">Essa ação não pode ser desfeita.</p>
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm">Excluir</button>
        </div>
    </form>
</div>
@endsection
