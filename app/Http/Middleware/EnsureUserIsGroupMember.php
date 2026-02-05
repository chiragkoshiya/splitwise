<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsGroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if ($group instanceof Group) {
            $isMember = $group->users()->where('user_id', $request->user()->id)->exists();

            if (! $isMember) {
                abort(403, 'You are not a member of this group.');
            }
        }

        return $next($request);
    }
}
