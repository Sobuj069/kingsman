<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SetActiveBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disabling automatic branch setting to force user selection
        /*
        if (Auth::check() && !Session::has('branch_id')) {
            Session::put('branch_id', Auth::user()->branch_id);
        }
        */
        return $next($request);
    }
}
