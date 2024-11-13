<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($request->bearerToken())
                throw new CustomException('Invalid request.');

            return $next($request);

        } catch (CustomException $e) {
            return ResponseHelper::make(
                status: 'error',
                message: $e->getMessage(),
            );
        }
    }
}
