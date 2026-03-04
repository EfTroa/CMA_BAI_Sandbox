<?php

namespace App\Http\Middleware;

use App\Services\Logging\ActionLogService;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware that logs every HTTP request.
 *
 * Pedagogical goals:
 * - Introduce logging pipelines
 * - Show how middleware works
 * - Enable students to identify excessive logging
 * - Prepare exercises to reduce log noise!
 */
class ActionLogMiddleware
{
    public function __construct(
        private readonly ActionLogService $logger
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user   = $request->user();
        $userId = $user ? $user->id : null;
        $status = $response->getStatusCode();

        $action = sprintf(
            'HTTP %s %s',
            $request->getMethod(),
            $request->path()
        );

        // Déduit le résultat à partir du code HTTP retourné
        $result = match (true) {
            $status >= 500 => 'error',
            $status >= 400 => 'failure',
            default        => 'success',
        };

        $this->logger->log(
            userId: $userId,
            action: $action,
            request: $request,
            result: $result,
            httpStatus: $status,
        );

        return $response;
    }
}
