<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function current()
    {
        $subscription = auth()->user()->activeSubscription();

        if (!$subscription) {
            return response()->json(['message' => 'Nenhuma assinatura ativa'], 404);
        }

        return response()->json($subscription);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
        ]);

        $plans = [
            'basic' => 29.90,
            'premium' => 59.90,
            'enterprise' => 99.90,
        ];

        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'plan' => $validated['plan'],
            'starts_at' => now(),
            'ends_at' => now()->addMonths(1),
            'price' => $plans[$validated['plan']],
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Assinatura criada', 'subscription' => $subscription], 201);
    }

    public function cancel()
    {
        $subscription = auth()->user()->activeSubscription();

        if (!$subscription) {
            return response()->json(['message' => 'Nenhuma assinatura ativa'], 404);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Assinatura cancelada']);
    }
}
