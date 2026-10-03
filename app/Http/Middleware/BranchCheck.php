<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Auto-set branch in session if not already set (bypassing select branch screen)
            if (!session()->has('branch_id')) {
                $userBranchId = $user->getRawOriginal('branch_id') ?: 1;
                $branch = \App\Models\Branch::find($userBranchId) ?: \App\Models\Branch::first();
                if ($branch) {
                    session([
                        'branch_id'        => $branch->id,
                        'branch_name'      => $branch->name,
                        'branch_filter_id' => $branch->id
                    ]);
                }
            }
        }

        return $next($request);
    }
}
