<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCustomers
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // بررسی برای نقش ادمین یا مدیر فروش
        if ($user->hasAnyAdminRole()) {
            return $next($request);
        }

        if (!$user->customers()->exists()) {
            return response()->json(['error' => 'هیچ مشتری برای نمایش به کاربر وجود ندارد'], 404);
        }

        return $next($request);
    }
}
