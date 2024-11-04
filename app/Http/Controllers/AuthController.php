<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Facades\Permission;
use App\Facades\UserRole;
use App\Helper\ResponseHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $creds = $request->only('email', 'password');

            if (! $token = Auth::attempt($creds))
                throw new CustomException('Credentials don\'t match.');

            return ResponseHelper::make(
                data: $this->getTokenData($token),
                message: 'Login successful.'
            );

        } catch (ValidationException|CustomException $ce) {
            return ResponseHelper::make(
                status: 'error',
                message: $ce->getMessage()
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function me(): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: [
                    'me' => Auth::user(),
                    'roles' => UserRole::getRoles(),
                    'permissions' => Permission::getPermissions()
                ]);

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function getRefreshToken(): JsonResponse
    {
        try {
            $token = Auth::refresh(true, true);

            return ResponseHelper::make(
                status: 'success',
                data: $this->getTokenData($token),
                message: 'Token refreshed.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function logout(): JsonResponse
    {
        try {
            Auth::logout(true);

            return ResponseHelper::make(
                message: 'Logout successful.'
            );
        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    private function getTokenData($token): array
    {
        return [
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}
