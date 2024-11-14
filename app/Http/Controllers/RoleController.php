<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $roles = Role::with('permissions')->get();

            return ResponseHelper::make(
                status: 'success',
                data: $roles,
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:roles,slug',
            ]);

            $role = Role::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $role,
                message: 'Role created successfully',
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $role->load('permissions'),
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'slug' => "required|unique:roles,slug,{$role->id}",
            ]);

            $role->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $role,
                message: 'Role updated successfully',
            );

        } catch (ValidationException $e) {
            $message = $e->getMessage();

        } catch (Exception $e) {
            $message = 'Unexpected error occurred.';
        }

        return ResponseHelper::make(
            status: 'error',
            message: $message,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $role->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Role deleted successfully',
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }
}
