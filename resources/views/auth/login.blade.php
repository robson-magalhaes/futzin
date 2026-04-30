<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Entrar — Futzin</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full bg-slate-950 text-white">
<div class="min-h-full flex">

    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden bg-linear-to-br from-slate-900 via-emerald-950 to-slate-950">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,rgba(16,185,129,0.15)_0%,transparent_60%)]"></div>
        <div class="relative z-10 flex flex-col justify-between p-12">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-linear-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-xl shadow-emerald-900/50">
                    ⚽
                </div>
                <span class="text-xl font-bold tracking-tight">Futzin</span>
            </div>

            <div class="space-y-8">
                <div>
                    <h2 class="text-4xl font-bold text-white leading-tight">
                        Organize seu futebol<br>
                        <span class="text-emerald-400">com estilo</span>
                    </h2>
                    <p class="mt-4 text-slate-400 text-lg leading-relaxed">
                        Gerencie grupos, partidas, rankings e muito mais com a plataforma favorita dos peladas.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-800/40 backdrop-blur rounded-xl p-4 border border-slate-700/30">
                        <div class="text-2xl font-bold text-emerald-400">Rankings</div>
                        <div class="text-sm text-slate-400 mt-1">Pontuação automática por gols, assistências e MVP</div>
                    </div>
                    <div class="bg-slate-800/40 backdrop-blur rounded-xl p-4 border border-slate-700/30">
                        <div class="text-2xl font-bold text-blue-400">Grupos</div>
                        <div class="text-sm text-slate-400 mt-1">Convide amigos com código único e gerencie presenças</div>
                    </div>
                    <div class="bg-slate-800/40 backdrop-blur rounded-xl p-4 border border-slate-700/30">
                        <div class="text-2xl font-bold text-purple-400">Partidas</div>
                        <div class="text-sm text-slate-400 mt-1">Agende e finalize partidas com estatísticas detalhadas</div>
                    </div>
                    <div class="bg-slate-800/40 backdrop-blur rounded-xl p-4 border border-slate-700/30">
                        <div class="text-2xl font-bold text-amber-400">Financeiro</div>
                        <div class="text-sm text-slate-400 mt-1">Controle mensalidades e pagamentos do grupo</div>
                    </div>
                </div>
            </div>

            <p class="text-slate-600 text-sm">© {{ date('Y') }} Futzin. Todos os direitos reservados.</p>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="relative flex-1 flex items-center justify-center p-6 sm:p-8">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_15%,rgba(59,130,246,0.14),transparent_35%),radial-gradient(circle_at_20%_80%,rgba(16,185,129,0.14),transparent_40%)]"></div>
        <div class="w-full max-w-md">
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-8 h-8 bg-linear-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center text-base">⚽</div>
                <span class="font-bold text-lg">Futzin</span>
            </div>

            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 backdrop-blur p-6 sm:p-7 shadow-2xl shadow-black/20">
                <h1 class="text-2xl font-bold text-white mb-1">Bem-vindo de volta</h1>
                <p class="text-slate-400 text-sm mb-6">Entre com sua conta para continuar</p>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-slate-800 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-700' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="seu@email.com">
                        @error('email')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-slate-300">Senha</label>
                        </div>
                        <input type="password" name="password" required
                               class="w-full bg-slate-800 border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-700' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                        @error('password')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember"
                               class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-900">
                        <label for="remember" class="text-sm text-slate-400">Lembrar de mim</label>
                    </div>

                    <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-900/30 hover:shadow-emerald-900/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Entrar
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-500">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                    Criar conta grátis
                </a>
            </p>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') {
            return;
        }

        @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: @json(session('success')),
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
            background: '#0f172a',
            color: '#e2e8f0'
        });
        @endif

        @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Falha no login',
            text: @json($errors->first()),
            confirmButtonColor: '#10b981',
            background: '#0f172a',
            color: '#e2e8f0'
        });
        @endif
    });
</script>
</body>
</html>
