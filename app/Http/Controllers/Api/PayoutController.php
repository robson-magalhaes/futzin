<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function myPayouts()
    {
        $payouts = Payout::where('user_id', auth()->id())
            ->with('group')
            ->orderByDesc('due_date')
            ->get();

        return response()->json($payouts);
    }

    public function groupPayouts($groupId)
    {
        $group = auth()->user()->ownedGroups()->findOrFail($groupId);

        $payouts = Payout::where('group_id', $groupId)
            ->with('user')
            ->orderByDesc('due_date')
            ->get();

        return response()->json($payouts);
    }

    public function markAsPaid(Payout $payout, Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string',
        ]);

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        return response()->json(['message' => 'Pagamento registrado', 'payout' => $payout]);
    }
}
