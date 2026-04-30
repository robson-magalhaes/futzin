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

    {{-- Ranking config --}}
    <form method="POST" action="{{ route('groups.update', $group) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
        @csrf
        @method('PUT')
        {{-- repassa os campos obrigatórios --}}
        <input type="hidden" name="name"        value="{{ $group->name }}">
        <input type="hidden" name="monthly_fee" value="{{ $group->monthly_fee }}">
        <input type="hidden" name="status"      value="{{ $group->status }}">

        <div>
            <h3 class="text-white font-semibold mb-1">Tipo de Ranking</h3>
            <p class="text-slate-500 text-xs mb-4">Configure os pesos de cada estatística na pontuação total. Defina 0 para ignorar uma métrica.</p>
        </div>

        @php
        $cfg = $group->rankingConfig();
        $presets = [
            'geral'  => ['label' => 'Geral (padrão)',     'cfg' => ['win_weight'=>3,'penalty_weight'=>1,'mvp_weight'=>5,'rating_weight'=>2]],
            'vitoria' => ['label' => 'Foco em vitória',   'cfg' => ['win_weight'=>8,'penalty_weight'=>1,'mvp_weight'=>2,'rating_weight'=>1]],
            'enquete' => ['label' => 'Foco em enquete',   'cfg' => ['win_weight'=>1,'penalty_weight'=>1,'mvp_weight'=>8,'rating_weight'=>8]],
            'custom' => ['label' => 'Personalizado',      'cfg' => null],
        ];
        @endphp

        {{-- Presets --}}
        <div>
            <p class="text-xs text-slate-400 mb-2">Atalhos</p>
            <div class="flex flex-wrap gap-2" id="presets">
                @foreach($presets as $key => $preset)
                <button type="button" data-preset="{{ $key }}" data-cfg='@json($preset["cfg"])'
                    class="preset-btn px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-700 text-slate-300 hover:border-emerald-500 hover:text-emerald-300 transition-colors">
                    {{ $preset['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Pesos --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach(['win_weight' => 'Vitória', 'penalty_weight' => 'Penalidade', 'mvp_weight' => 'Votos MVP', 'rating_weight' => 'Nota média'] as $field => $label)
            <div>
                <label class="block text-xs text-slate-400 mb-1">{{ $label }}</label>
                <input type="number" step="0.5" min="0" max="100" id="field_{{ $field }}"
                    name="ranking_config[{{ $field }}]"
                    value="{{ old('ranking_config.'.$field, $cfg[$field]) }}"
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm">
            </div>
            @endforeach
        </div>

        <p class="text-xs text-slate-500">Fórmula: <span class="font-mono text-slate-300">(nota_média × peso_nota) + (votos_mvp × peso_mvp) + (vitórias × peso_vitória) + (penalidades_negativas × peso_penalidade)</span></p>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg font-medium text-sm">Salvar Tipo de Ranking</button>
        </div>
    </form>

    <script>
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const cfg = JSON.parse(this.dataset.cfg || 'null');
            if (!cfg) return; // custom: não altera campos
            Object.entries(cfg).forEach(([k, v]) => {
                const el = document.getElementById('field_' + k);
                if (el) el.value = v;
            });
        });
    });
    </script>

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
