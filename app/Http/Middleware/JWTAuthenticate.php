<?php

namespace App\Http\Middleware;

use App\Facades\Permission;
use App\Facades\UserRole;
use App\Helper\ResponseHelper;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();

            $token = JWTAuth::getToken();
            $claims = JWTAuth::getPayload($token)->toArray();

            Permission::setPermissions($claims['permissions']);
            UserRole::setRoles($claims['roles']);

            return $next($request);

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unauthenticated.',
                code: 401
            );
        }
    }
}
