<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCategorySelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('main_category_id') && Auth::check()) {

            if (!$request->is('admin/select-category') && !$request->is('admin/logout')) {
                return redirect('/admin/select-category');
            }
        }
    
        return $next($request);
    }
}
