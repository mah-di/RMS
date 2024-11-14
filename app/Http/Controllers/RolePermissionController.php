<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function attach(Role $role, Permission $permission)
    {
        try {
            $role->permissions()->syncWithoutDetaching([$permission->id]);

            return ResponseHelper::make(
                status: 'success',
                message: "Permission ({$permission->getName()}) attached to Role ({$role->getName()}) successfully."
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function detach(Role $role, Permission $permission)
    {
        try {
            if ($role->slug === 'owner')
                throw new CustomException('Owner role can\'t be detached.');

            $role->permissions()->detach($permission->id);

            return ResponseHelper::make(
                status: 'success',
                message: "Permission ({$permission->getName()}) detached from Role ({$role->getName()}) successfully."
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

    public function bulkAttach(Role $role, string $slug)
    {
        try {
            $permissions = Permission::where('slug', '=', $slug)->get();

            DB::beginTransaction();

            foreach ($permissions as $permission)
                $this->attach($role, $permission);

            DB::commit();

            return ResponseHelper::make(
                status: 'success',
                message: "Permissions attached to Role ({$role->getName()}) successfully."
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function bulkDetach(Role $role, string $slug)
    {
        try {
            if ($role->slug === 'owner')
                throw new CustomException('Owner role can\'t be detached.');

            $permissions = Permission::where('slug', '=', $slug)->get();

            DB::beginTransaction();

            foreach ($permissions as $permission)
                $this->detach($role, $permission);

            DB::commit();

            return ResponseHelper::make(
                status: 'success',
                message: "Permissions detached from Role ({$role->getName()}) successfully."
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
