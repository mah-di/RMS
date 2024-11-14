<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Helper\ResponseHelper;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function attach(User $user, Role $role)
    {
        try {
            $user->roles()->syncWithoutDetaching([$role->id]);

            return ResponseHelper::make(
                status: 'success',
                message: "Role '{$role->name}' attached to {$user->fullName} successfully"
            );

        } catch (Exception $e) {
            return ResponseHelper::make(
                status: 'error',
                message: 'Unexpected error occurred.'
            );
        }
    }

    public function detach(User $user, Role $role)
    {
        try {
            if ($role->slug === 'owner')
                throw new CustomException('Owner role can\'t be detached.');

            $user->roles()->detach($role->id);

            return ResponseHelper::make(
                status: 'success',
                message: "Role '{$role->name}' detached from {$user->fullName} successfully"
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
