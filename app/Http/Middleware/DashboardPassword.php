<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardPassword
{
    public function handle(Request $request, Closure $next)
    {
        // 30 daqiqa ichida unlock bo‘lganini tekshir
//        if (
//            session('dashboard_unlocked') &&
//            session('dashboard_unlocked_at') &&
//            now()->diffInMinutes(session('dashboard_unlocked_at')) < 30
//        ) {
//            return $next($request);
//        }
//
//        // POST so‘rov – parolni tekshir
//        if ($request->isMethod('post') && $request->routeIs('dashboard.unlock')) {
//            if (hash_equals(config('app.dashboard_password'), $request->input('password'))) {
//                session([
//                    'dashboard_unlocked' => true,
//                    'dashboard_unlocked_at' => now(),
//                ]);
//
//                return redirect()->intended(route('filament.admin.pages.dashboard'));
//            }
//
//            return back()->withErrors(['password' => 'Parol noto‘g‘ri']);
//        }
//
//        // Aks holda parol sahifasini ko‘rsat
//        return response()->view('filament.components.auth');

        return $next($request);
    }
}
