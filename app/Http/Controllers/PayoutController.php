<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = Payout::where('user_id', auth()->id())
            ->with('group')
            ->orderByDesc('due_date')
            ->paginate(20);

        return view('payouts.index', compact('payouts'));
    }

    public function groupPayouts(Group $group)
    {
        abort_if($group->user_id !== auth()->id(), 403, 'Apenas o dono do grupo pode ver pagamentos.');

        $payouts = Payout::where('group_id', $group->id)
            ->with('user')
            ->orderByDesc('due_date')
            ->paginate(30);

        return view('payouts.group', compact('group', 'payouts'));
    }

    public function markAsPaid(Request $request, Payout $payout)
    {
        $group = $payout->group;
        $isOwner  = $group->user_id === auth()->id();
        $isSelf   = $payout->user_id === auth()->id();
        abort_if(!$isOwner && !$isSelf, 403, 'Sem permissão.');

        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:100',
        ]);

        $payout->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        return back()->with('success', 'Pagamento registrado com sucesso!');
    }
}
