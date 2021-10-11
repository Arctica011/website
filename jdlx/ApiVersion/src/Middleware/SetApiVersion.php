<?php

namespace Jdlx\ApiVersion\Middleware;

use App\Library\ApiVersion\Facade\Version;
use Closure;

class SetApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $version = $request->route()->parameter('version', 'v1');

        // remove parameter so it is not accidentally injected into routes
        if (isset($request->route()->parameters['version'])) {
            unset($request->route()->parameters['version']);
        };

        try {
            Version::set($version);
            if (Version::number() > 1) {
                return response()->json(["message" => "Not found"], 404);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json(["message" => "Not found"], 404);
        }


        // Get the response
        return $next($request);
    }
}
