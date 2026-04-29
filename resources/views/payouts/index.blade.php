@extends('layouts.app')

@section('title', 'Meus Pagamentos')
@section('page-title', 'Meus Pagamentos')

@section('content')
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
            <tr class="text-left text-slate-500 border-b border-slate-800">
                <th class="px-4 py-3">Grupo</th>
                <th class="px-4 py-3">Vencimento</th>
                <th class="px-4 py-3">Valor</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Pagamento</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payouts as $payout)
            <tr class="border-b border-slate-800/70">
                <td class="px-4 py-3 text-white">{{ $payout->group->name }}</td>
                <td class="px-4 py-3 text-slate-300">{{ $payout->due_date?->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-slate-300">R$ {{ number_format((float) $payout->amount, 2, ',', '.') }}</td>
                <td class="px-4 py-3 {{ $payout->status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $payout->status === 'paid' ? 'Pago' : 'Pendente' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $payout->payment_method ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum pagamento encontrado.</td>
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
