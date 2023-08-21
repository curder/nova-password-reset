<?php

namespace Mastani\NovaPasswordReset\Http\Middleware;

use Laravel\Nova\Nova;
use Mastani\NovaPasswordReset\PasswordReset;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param  \Laravel\Nova\Tool  $tool
     */
    public function matchesTool($tool): bool
    {
        return $tool instanceof PasswordReset;
    }
}
