<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInvoices
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // بررسی برای نقش ادمین یا مدیر فروش
        if ($user->hasAnyAdminRole()) {
            return $next($request);
        }

        // بررسی برای وجود فاکتورها
        if (!$user->invoices()->exists()) {
            return response()->json(['error' => 'فاکتوری برای نمایش به کاربر وجود ندارد'], 404);
        }

        return $next($request);
    }
}
