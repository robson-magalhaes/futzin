@extends('layouts.app')

@section('title', 'Assinatura')
@section('page-title', 'Assinatura')

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        @if($subscription)
        <p class="text-sm text-slate-400">Plano atual</p>
        <p class="text-xl font-semibold text-emerald-400 mt-1">{{ ucfirst($subscription->plan) }}</p>
        <p class="text-xs text-slate-500 mt-1">Válido até {{ $subscription->ends_at?->format('d/m/Y') }}</p>
        <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-4">
            @csrf
            <button class="bg-red-600 hover:bg-red-500 text-white text-sm px-4 py-2 rounded-lg">Cancelar Assinatura</button>
        </form>
        @else
        <p class="text-slate-400 text-sm">Você não possui assinatura ativa.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @foreach($plans as $key => $plan)
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-white font-semibold">{{ $plan['name'] }}</h3>
            <p class="text-2xl font-bold text-emerald-400 mt-1">R$ {{ number_format((float) $plan['price'], 2, ',', '.') }}<span class="text-sm text-slate-500">/mês</span></p>
            <ul class="mt-4 space-y-1.5 text-sm text-slate-300">
                @foreach($plan['features'] as $feature)
                <li>• {{ $feature }}</li>
                @endforeach
            </ul>
            <form method="POST" action="{{ route('subscription.subscribe') }}" class="mt-5">
                @csrf
                <input type="hidden" name="plan" value="{{ $key }}">
                <button class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-lg font-medium">Assinar {{ $plan['name'] }}</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
