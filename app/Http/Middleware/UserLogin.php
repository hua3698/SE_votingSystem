<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->session()->get('frontuser');

        if (!$email) {
            $request->session()->put('url.intended', $request->fullUrl());
            return redirect()->route('user.login.form');
        }

        return $next($request);
    }
}
