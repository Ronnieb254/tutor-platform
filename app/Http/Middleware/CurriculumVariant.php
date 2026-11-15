<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CurriculumVariant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get curriculum variant from query parameter or session
        $variant = $request->query('variant');
        
        if ($variant && in_array($variant, ['kenyan', 'international'])) {
            // Store in session for persistence
            session(['curriculum_variant' => $variant]);
        } else {
            // Use stored variant or default to 'kenyan'
            $variant = session('curriculum_variant', 'kenyan');
        }
        
        // Share with all views
        View::share('curriculum_variant', $variant);
        View::share('isInternational', $variant === 'international');
        View::share('isKenyan', $variant === 'kenyan');
        
        // Add curriculum query parameter to request for easy access in views
        $request->merge(['_curriculum_variant' => $variant]);
        
        return $next($request);
    }
}
