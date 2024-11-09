<?php

namespace App\Http\Middleware;

use App\Facades\Permission;
use App\Facades\UserRole;
use App\Helper\ResponseHelper;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): JsonResponse|Response
    {
        try {
            $this->authenticate($request);

            $this->setContext();

            $response = $next($request);

            return $this->setAuthenticationHeader($response);

        } catch (JWTException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unauthenticated.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message,
            code: 401
        );
    }

    public function setContext()
    {
        $token = JWTAuth::getToken();
        $claims = JWTAuth::getPayload($token)->toArray();

        Permission::setPermissions($claims['permissions']);
        UserRole::setRoles($claims['roles']);
    }
}
