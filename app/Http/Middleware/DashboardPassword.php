<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardPassword
{
    public function handle(Request $request, Closure $next)
    {
        // Avvalgi tekshiruv
        if (session('dashboard_unlocked')) {
            return $next($request);
        }

        // Formadan POST kelgan bo‘lsa, parolni tekshir
        if ($request->isMethod('post') && $request->routeIs('dashboard.unlock')) {
            if (hash_equals(config('app.dashboard_password'), $request->input('password'))) {
                session(['dashboard_unlocked' => true]);
                return redirect()->intended(route('filament.admin.pages.dashboard'));
            }

            return back()->withErrors(['password' => 'Parol noto‘g‘ri']);
        }

        // Aks holda parol so‘raladigan sahifani ko‘rsat
        return response()->view('filament.components.auth');
    }
}
