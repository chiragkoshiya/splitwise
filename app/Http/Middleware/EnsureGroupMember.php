<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract group from route parameter
        $group = $request->route('group');
        
        // If group is an ID, load the model
        if (is_numeric($group)) {
            $group = Group::findOrFail($group);
        }
        
        // If no group found, abort
        if (!$group || !($group instanceof Group)) {
            abort(400, 'Group parameter required');
        }
        
        // SECURITY: Verify user is a member of this group
        if (!$group->users()->where('users.id', auth()->id())->exists()) {
            abort(403, 'Access denied. You are not a member of this group.');
        }
        
        // Attach verified group to request for convenience
        $request->merge(['_verifiedGroup' => $group]);
        
        return $next($request);
    }
}
