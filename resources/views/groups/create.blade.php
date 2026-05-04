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
            <label class="block text-sm text-slate-300 mb-2">Tipo de cobrança</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="fee_type" value="monthly" class="sr-only peer" {{ old('fee_type', 'monthly') === 'monthly' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center gap-1.5 px-4 py-3 rounded-xl border border-slate-700 bg-slate-800 text-slate-400 text-sm font-medium transition-colors peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Mensal
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="fee_type" value="daily" class="sr-only peer" {{ old('fee_type') === 'daily' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center gap-1.5 px-4 py-3 rounded-xl border border-slate-700 bg-slate-800 text-slate-400 text-sm font-medium transition-colors peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Diário (por pelada)
                    </div>
                </label>
            </div>
        </div>

        <div x-data="feeLabel()" x-init="init()">
            <label class="block text-sm text-slate-300 mb-1.5" x-text="label"></label>
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

@push('scripts')
<script>
function feeLabel() {
    return {
        label: 'Mensalidade (R$)',
        init() {
            const radios = document.querySelectorAll('input[name="fee_type"]');
            const update = () => {
                const val = document.querySelector('input[name="fee_type"]:checked')?.value;
                this.label = val === 'daily' ? 'Valor por pelada (R$)' : 'Mensalidade (R$)';
            };
            radios.forEach(r => r.addEventListener('change', update));
            update();
        }
    };
}
</script>
@endpush
