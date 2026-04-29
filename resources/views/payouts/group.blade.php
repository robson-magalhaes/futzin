@extends('layouts.app')

@section('title', 'Pagamentos do Grupo')
@section('page-title', 'Pagamentos - ' . $group->name)
@section('breadcrumb', 'Grupos / Pagamentos')

@section('content')
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
            <tr class="text-left text-slate-500 border-b border-slate-800">
                <th class="px-4 py-3">Jogador</th>
                <th class="px-4 py-3">Vencimento</th>
                <th class="px-4 py-3">Valor</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Ação</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payouts as $payout)
            <tr class="border-b border-slate-800/70">
                <td class="px-4 py-3 text-white">{{ $payout->user->name }}</td>
                <td class="px-4 py-3 text-slate-300">{{ $payout->due_date?->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-slate-300">R$ {{ number_format((float) $payout->amount, 2, ',', '.') }}</td>
                <td class="px-4 py-3 {{ $payout->status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $payout->status === 'paid' ? 'Pago' : 'Pendente' }}</td>
                <td class="px-4 py-3">
                    @if($payout->status !== 'paid')
                    <form method="POST" action="{{ route('payouts.mark-paid', $payout) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="payment_method" placeholder="PIX, Dinheiro..." class="bg-slate-800 border border-slate-700 rounded-md px-2.5 py-1.5 text-xs text-white">
                        <button class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs px-3 py-1.5 rounded-md">Marcar pago</button>
                    </form>
                    @else
                    <span class="text-xs text-slate-500">{{ $payout->paid_at?->format('d/m H:i') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum pagamento para este grupo.</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-slate-800">
        {{ $payouts->links() }}
    </div>
</div>
@endsection
