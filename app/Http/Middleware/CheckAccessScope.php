<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Traits\HasClaims;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessScope
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
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        try {
            if (! array_key_exists('scope', $this->claims) || $this->claims['scope'] !== $scope)
                throw new CustomException('Access denied.');

            return $next($request);

        } catch (CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'unauthorized',
            message: $message,
            code: 403
        );
    }
}
