<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CurriculumVariant
{
    /**
     * Inject the curriculum variant into every view.
     *
     * Priority (highest to lowest):
     *  1. ?variant= query param  → updates session immediately
     *  2. PHP session             → survives page navigation within a browser tab
     *  3. Authenticated user DB   → survives across devices / new sessions
     *  4. Default: 'kenyan'
     */
    public function handle(Request $request, Closure $next)
    {
        $valid = ['kenyan', 'international'];

        // 1. Explicit query-param override — write through to session
        $param = $request->query('variant');
        if ($param && in_array($param, $valid)) {
            session([
                'curriculum_variant' => $param,
                'curriculum_track'   => $param,
            ]);

            if (auth()->check() && auth()->user()->getAttribute('curriculum_preference') !== $param) {
                try {
                    auth()->user()->update(['curriculum_preference' => $param]);
                } catch (\Throwable) {
                    // Column may not exist on older deployments — silently skip.
                }
            }
        }

        // 2. Session (may have been set by query param above, by /curriculum/sync,
        //    or by /select-curriculum form).
        $variant = session('curriculum_variant') ?? session('curriculum_track');

        // 3. Authenticated user's saved preference (cross-device persistence).
        if (!$variant && auth()->check()) {
            try {
                $pref = auth()->user()->getAttribute('curriculum_preference');
                if ($pref && in_array($pref, $valid)) {
                    $variant = $pref;
                    // Backfill session so we don't hit the DB again this request.
                    session([
                        'curriculum_variant' => $pref,
                        'curriculum_track'   => $pref,
                    ]);
                }
            } catch (\Throwable) {
                // Silently skip if column does not exist.
            }
        }

        // 4. Default
        $variant = (in_array($variant, $valid) ? $variant : null) ?? 'kenyan';

        // Share with every Blade view in this request.
        View::share('curriculum_variant', $variant);
        View::share('isInternational', $variant === 'international');
        View::share('isKenyan', $variant === 'kenyan');
        View::share('curriculumTrack', $variant); // unified alias

        return $next($request);
    }
}
