<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use App\Facades\Permission;
use App\Helper\ResponseHelper;
use App\Facades\UserRole;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        try {
            if (! UserRole::hasRole('owner') && ! Permission::hasPermissionTo($permission))
                throw new CustomException('You don\'t have permission to access this resource.');

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
