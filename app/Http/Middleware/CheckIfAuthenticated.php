<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->session()->get('email');

        // 判斷是否是 AJAX 請求或是想要 Modal 的狀況
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$email) {
            return redirect()->route('login.form');
        }

        return $next($request);
    }
}
