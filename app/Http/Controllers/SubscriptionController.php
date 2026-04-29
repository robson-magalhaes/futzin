<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    private array $plans = [
        'basic' => ['name' => 'Básico', 'price' => 29.90, 'features' => ['Até 2 grupos', 'Até 20 membros por grupo', 'Histórico de 3 meses', 'Suporte por e-mail']],
        'premium' => ['name' => 'Premium', 'price' => 59.90, 'features' => ['Grupos ilimitados', 'Membros ilimitados', 'Histórico completo', 'Rankings avançados', 'Suporte prioritário']],
        'enterprise' => ['name' => 'Enterprise', 'price' => 99.90, 'features' => ['Tudo do Premium', 'API de integração', 'Relatórios exportáveis', 'Gestor dedicado', 'SLA garantido']],
    ];

    public function index()
    {
        $subscription = auth()->user()->activeSubscription();
        $plans = $this->plans;

        return view('subscriptions.index', compact('subscription', 'plans'));
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
        ]);

        $existing = auth()->user()->activeSubscription();
        if ($existing) {
            $existing->update(['status' => 'cancelled']);
        }

        Subscription::create([
            'user_id' => auth()->id(),
            'plan' => $validated['plan'],
            'starts_at' => now(),
            'ends_at' => now()->addMonths(1),
            'price' => $this->plans[$validated['plan']]['price'],
            'status' => 'active',
        ]);

        $planName = $this->plans[$validated['plan']]['name'];

        return redirect()->route('subscription.index')
            ->with('success', "Plano {$planName} ativado com sucesso!");
    }

    public function cancel()
    {
        $subscription = auth()->user()->activeSubscription();

        if (!$subscription) {
            return back()->withErrors(['subscription' => 'Nenhuma assinatura ativa encontrada.']);
        }

        $subscription->update(['status' => 'cancelled']);

        return redirect()->route('subscription.index')
            ->with('success', 'Assinatura cancelada com sucesso.');
    }
}
