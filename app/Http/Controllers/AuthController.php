<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Facades\Permission;
use App\Facades\UserRole;
use App\Helper\ResponseHelper;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

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

            if (! Auth::user()->email_verified_at)
                $token = $this->nonRefreshableToken('verification');

            return ResponseHelper::make(
                data: $this->getTokenData($token),
                message: 'Login successful.'
            );

        } catch (ValidationException|CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
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
            $token = $this->refresh(true, true);

            return ResponseHelper::make(
                status: 'success',
                data: $this->getTokenData($token),
                message: 'Token refreshed.'
            );

        } catch (TokenExpiredException $e) {
            $status = 'expired';
            $message = 'Refresh Time Expired.';

        } catch (Exception $e) {
            $status = 'error';
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: $status,
            message: $message
        );
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:8',
            ]);

            if (! Hash::check($request->old_password, Auth::user()->password))
                throw new CustomException('Old password doesn\'t match.');

            Auth::user()->update([
                'password' => bcrypt($request->new_password)
            ]);

            return ResponseHelper::make(
                status: 'success',
                message: 'Password changed successfully.'
            );

        } catch (CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    public function sendOTP(Request $request): JsonResponse
    {
        try {
            if (! $user = Auth::user()) {
                $request->validate(['email' => 'required|email']);

                if (! $user = User::where('email', $request->email)->first())
                    throw new CustomException('User not found.');
            }

            $otp = random_int(100000, 999999);

            $user->update(['otp' => $otp]);

            $type = $request->routeIs('send.otp') ? 'pass-reset' : 'verification';
            $mail = new OTPMail($type, $otp);

            Mail::to($user->email)->send($mail);

            return ResponseHelper::make(
                status: 'success',
                message: '6 digit OTP has been sent sent to your email. Expires in 5 minutes.'
            );

        } catch (ValidationException|CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
            $message = $e->getMessage();
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $request->validate(['otp' => 'required|numeric|digits:6']);

            if (Auth::user()->updated_at->addMinutes(5)->addSeconds(10) < now()) {
                Auth::user()->update(['otp' => null]);

                throw new CustomException('OTP has been expired.');
            }

            if (Auth::user()->otp != $request->otp)
                throw new CustomException('OTP doesn\'t match.');

            Auth::user()->update([
                'otp' => null,
                'email_verified_at' => now()
            ]);

            return ResponseHelper::make(
                status: 'success',
                data: $this->getTokenData($this->refresh(true, true)),
                message: 'Email verified successfully.'
            );

        } catch (ValidationException|CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    public function verifyPassResetOTP(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|numeric|digits:6'
            ]);

            if (! $user = User::where('email', $request->email)->first())
                throw new CustomException('User not found.');

            if ($user->updated_at->addMinutes(5)->addSeconds(10) < now()) {
                $user->update(['otp' => null]);

                throw new CustomException('OTP has been expired.');
            }

            if ($user->otp != $request->otp)
            throw new CustomException('OTP doesn\'t match.');

            $user->update(['otp' => null]);
            Auth::setUser($user);

            $token = $this->nonRefreshableToken('pass-reset');;

            return ResponseHelper::make(
                status: 'success',
                data: $this->getTokenData($token),
                message: 'OTP verified successfully.'
            );

        } catch (ValidationException|CustomException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $request->validate(['password' => 'required|min:8']);

            Auth::user()->update([
                'password' => bcrypt($request->password),
                Auth::user()->email_verified_at ?? 'email_verified_at' => now()
            ]);

            Auth::invalidate(true);

            return ResponseHelper::make(
                status: 'success',
                message: 'Password reset successfully.'
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message
        );
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

    public function refresh(bool $forceForever = false, bool $resetClaims = false): mixed
    {
        return Auth::refresh($forceForever, $resetClaims);
    }

    public function nonRefreshableToken(string $scope)
    {
        Auth::factory()->setTTL(10);

        return Auth::claims([
                'exp' => time() + (10 * 60),
                'scope' => $scope,
                'non_refreshable' => true
            ])
            ->fromUser(Auth::user());
    }

    private function getTokenData(string $token): array
    {
        return [
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}
