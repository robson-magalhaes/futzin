<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSubscriptionIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->hasActiveSubscription()) {
            return response()->json(['message' => 'Você precisa de uma assinatura ativa'], 403);
        }

        return $next($request);
    }
}
