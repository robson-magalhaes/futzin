@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')
@section('breadcrumb', 'Perfil')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">

        @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-900/40 border border-emerald-700 text-emerald-300 text-sm">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label class="block text-sm text-slate-400 mb-1.5" for="name">Nome *</label>
                <input type="text" id="name" name="name" required maxlength="255"
                    value="{{ old('name', $user->name) }}"
                    class="w-full bg-slate-800 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700' }} rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                @error('name')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Posição --}}
            <div>
                <label class="block text-sm text-slate-400 mb-1.5" for="position">Posição</label>
                <select id="position" name="position"
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                    <option value="">— Não informado —</option>
                    @foreach(['Goleiro','Defensor','Lateral','Volante','Meia','Atacante','Ponta'] as $pos)
                    <option value="{{ $pos }}" {{ old('position', $user->position) === $pos ? 'selected' : '' }}>
                        {{ $pos }}
                    </option>
                    @endforeach
                </select>
                @error('position')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- E-mail (somente leitura) --}}
            <div>
                <label class="block text-sm text-slate-400 mb-1.5">E-mail</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-500 text-sm cursor-not-allowed">
                <p class="mt-1 text-xs text-slate-600">O e-mail não pode ser alterado.</p>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
