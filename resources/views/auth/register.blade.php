<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar conta — Futzin</title>
    @vite('resources/css/app.css')
</head>
<body class="h-full bg-slate-950 text-white">
<div class="min-h-full relative flex items-center justify-center p-6">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_10%,rgba(16,185,129,0.15),transparent_40%),radial-gradient(circle_at_90%_80%,rgba(59,130,246,0.12),transparent_35%)]"></div>
    <div class="w-full max-w-lg">

        <div class="flex items-center gap-2 mb-8">
            <div class="w-9 h-9 bg-linear-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-lg shadow-emerald-900/40">⚽</div>
            <span class="font-bold text-xl">Futzin</span>
        </div>

        <h1 class="text-2xl font-bold text-white mb-1">Criar sua conta</h1>
        <p class="text-slate-400 text-sm mb-8">Comece a organizar seu futebol hoje mesmo</p>

        <div class="bg-slate-900/75 backdrop-blur border border-slate-800 rounded-2xl p-6 shadow-2xl shadow-black/20">
            @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-700/40 bg-red-900/20 px-3.5 py-2.5 text-sm text-red-300">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Nome completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full bg-slate-800 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="Seu nome">
                        @error('name')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-slate-800 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-700' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="seu@email.com">
                        @error('email')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Telefone <span class="text-slate-500">(opcional)</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="(11) 99999-9999">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Posição <span class="text-slate-500">(opcional)</span></label>
                        <select name="position"
                                class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <option value="">Selecionar...</option>
                            <option value="Goleiro" {{ old('position') == 'Goleiro' ? 'selected' : '' }}>Goleiro</option>
                            <option value="Zagueiro" {{ old('position') == 'Zagueiro' ? 'selected' : '' }}>Zagueiro</option>
                            <option value="Lateral" {{ old('position') == 'Lateral' ? 'selected' : '' }}>Lateral</option>
                            <option value="Volante" {{ old('position') == 'Volante' ? 'selected' : '' }}>Volante</option>
                            <option value="Meia" {{ old('position') == 'Meia' ? 'selected' : '' }}>Meia</option>
                            <option value="Atacante" {{ old('position') == 'Atacante' ? 'selected' : '' }}>Atacante</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Senha</label>
                        <input type="password" name="password" required
                               class="w-full bg-slate-800 border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-700' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Confirmar senha</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="Repita a senha">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-900/30 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-950 mt-2">
                    Criar conta
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            Já tem uma conta?
            <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                Entrar
            </a>
        </p>
    </div>
</div>
</body>
</html>
