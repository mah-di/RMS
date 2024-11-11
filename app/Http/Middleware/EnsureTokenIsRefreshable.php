<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Traits\HasClaims;
use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsRefreshable
{
    use HasClaims;

    public function __construct()
    {
        $this->setClaims();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (array_key_exists('non_refreshable', $this->claims) && $this->claims['non_refreshable'])
                throw new CustomException('This token is not refreshable.');

            return $next($request);

        } catch (CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message,
            code: 401
        );
    }
}
