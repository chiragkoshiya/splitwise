<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleFinancialActions
{
    /**
     * Rate limits for various financial actions.
     * Format: 'route.name.pattern' => ['attempts' => int, 'decay' => seconds]
     */
    private array $limits = [
        'expenses.*' => ['attempts' => 10, 'decay' => 60],
        'settlements.*' => ['attempts' => 10, 'decay' => 60],
        'groups.create' => ['attempts' => 3, 'decay' => 300],
        'groups.members' => ['attempts' => 20, 'decay' => 60],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route() ? $request->route()->getName() : null;
        
        if (!$routeName) {
            return $next($request);
        }

        $userId = $request->user() ? $request->user()->id : $request->ip();
        
        foreach ($this->limits as $pattern => $limit) {
            if (fnmatch($pattern, $routeName)) {
                $key = "financial:{$userId}:{$routeName}";
                
                if (RateLimiter::tooManyAttempts($key, $limit['attempts'])) {
                    $seconds = RateLimiter::availableIn($key);
                    
                    if ($request->wantsJson()) {
                        return response()->json([
                            'message' => 'Too many requests. Please slow down.',
                            'retry_after' => $seconds
                        ], 429);
                    }
                    
                    abort(429, "Too many financial actions. Please try again in {$seconds} seconds.");
                }
                
                RateLimiter::hit($key, $limit['decay']);
                break; // Apply matching limit and stop checking
            }
        }
        
        return $next($request);
    }
}
