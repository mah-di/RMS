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
            $permissions = Permission::with('role')->get();

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'nullable',
                'slug' => 'required',
                'type' => 'required|in:view,create,update,delete',
                'role_id' => 'required|exists:roles,id',
            ]);

            $permission = Permission::create($validatedData);

            return ResponseHelper::make(
                status: 'success',
                data: $permission,
                message: 'Permission created successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
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
                data: $permission->load('role')
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
            $validatedData = $request->validate([
                'name' => 'nullable',
                'slug' => "required",
                'type' => 'required|in:view,create,update,delete',
                'role_id' => 'required|exists:roles,id',
            ]);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();

            return ResponseHelper::make(
                status: 'success',
                message: 'Permission deleted successfully.'
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }
}
