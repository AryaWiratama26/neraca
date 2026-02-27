<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Verify2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->google2fa_enabled && !session('2fa_verified')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
