<?php

namespace App\Http\Middleware;

use App\Facades\Permission;
use App\Facades\UserRole;
use App\Helper\ResponseHelper;
use App\Traits\HasClaims;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class JWTAuthenticate extends BaseMiddleware
{
    use HasClaims;

    public function __construct(JWTAuth $auth)
    {
        parent::__construct($auth);

        $this->setClaims();
    }

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

            if (array_key_exists('non_refreshable', $this->claims) && $this->claims['non_refreshable'])
                return $response;

            return $this->setAuthenticationHeader($response);

        } catch (JWTException $e) {
            $status = 'jwt-error';
            $message = $e->getMessage();

        } catch (Exception $e) {
            $status = 'unauthenticated';
            $message = 'Unauthenticated Request.';
        }

        return ResponseHelper::make(
            status: $status,
            message: $message,
            code: 401
        );
    }

    public function setContext()
    {
        Permission::setPermissions($this->claims['permissions']);
        UserRole::setRoles($this->claims['roles']);
    }
}
