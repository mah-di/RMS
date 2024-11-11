<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::with('roles')->get();

            return ResponseHelper::make(
                status: 'success',
                data: $users
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);

            $otp = random_int(100000, 999999);
            $validatedData['otp'] = $otp;

            $user = User::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $user,
                message: 'User created successfully! A 6 digit OTP has been sent to the user email. Expires in 5 minutes.'
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

    public function show(User $user): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $user
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
            ]);

            $user->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $user,
                message: 'User updated successfully!'
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

    public function destroy(User $user): JsonResponse
    {
        try {
            if ($user->hasRole('owner'))
                throw new CustomException('Owner can\'t be deleted.');

            $user->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'User deleted successfully!'
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
}
