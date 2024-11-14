<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\Permission;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $permissions = Permission::with('roles')->get();

            return ResponseHelper::make(
                status: 'success',
                data: $permissions,
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.',
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission): JsonResponse
    {
        try {
            return ResponseHelper::make(
                status: 'success',
                data: $permission->load('roles')
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        try {
            $validatedData = $request->validate(['name' => 'nullable']);

            $permission->update($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $permission,
                message: 'Permission updated successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }
}
