<!-- // app/Http/Middleware/RegionAccess.php -->
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Check if admin region is accessing their region data
        $resourceRegion = $request->route('cable')?->region ?? 
                         $request->route('closure')?->region ?? 
                         $request->input('region');
        
        if ($resourceRegion && $resourceRegion !== $user->region) {
            abort(403, 'Access denied to this region.');
        }
        
        return $next($request);
    }
}