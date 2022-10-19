<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\ModifyRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\RolesHasProcess;

class RoleController extends Controller
{
    /**
     * It creates a new role.
     *
     * @param CreateRoleRequest request The request object.
     */
    public function createRole(CreateRoleRequest $request)
    {
        try {
            $role = new Role();
            $role->name = $request->name;
            $role->name_slug = $request->name_slug;
            $role->description = $request->description;
            $role->state = 'A';
            $role->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Rol creado correctamente',
                    'data' => ['role' => $role],
                ],
                200,
            );
        } catch (Exception $exception) {
            response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It gets all the inactive roles from the database and returns them in a paginated format
     *
     * @return A JSON response with the roles and a status code.
     */
    public function getInactiveRoles()
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'I')
                ->latest()
                ->paginate(10);
            return response()->json(
                [
                    'roles' => $roles,
                ],
                200,
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It returns a paginated list of all the roles in the database that have a state of A.
     *
     * @return A JSON object with the following structure:
     * ```
     * {
     *     "roles": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "name": "Administrador",
     *                 "description": "Administrador del sistema",
     *                 "state": "A",
     *                 "created_
     */
    public function getActiveRoles()
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'A')
                ->where('name_slug', '!=', 'admin')
                ->latest()
                ->paginate(10);

            return response()->json(
                [
                    'roles' => $roles,
                ],
                200,
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * A function that allows you to delete a role, but it is not completely deleted, it is
     * deactivated.
     *
     * @param Role role The role to be deleted.
     */
    public function inactivateRole(Role $role)
    {
        try {
            $hasUsers = DB::table('users')
                ->where('role_id', $role->id)
                ->first();
            //dd($hasUsers);
            if (!$hasUsers) {
                $role->state = 'I';
                $role->save();
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Rol desactivado correctamente',
                        'data' => ['role' => $role],
                    ],
                    200,
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Este rol tiene usuarios asignados',
                    ],
                    400,
                );
            }
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It activates a role.
     *
     * @param Role role The role to be activated.
     */
    public function activateRole(Role $role)
    {
        try {
            $role->state = 'A';
            $role->save();
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Rol Activado correctamente',
                    'data' => ['role' => $role],
                ],
                200,
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It gets a role from the database
     *
     * @param Role role The role object that you want to get.
     */
    public function getRoleById(Role $role)
    {
        try {
            return response()->json(['success' => true, 'role' => $role], 200);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It takes a ModifyRoleRequest object and a Role object as parameters, and returns a JSON response
     *
     * @param ModifyRoleRequest request The request object.
     * @param Role role The role object that was passed in the route.
     */
    public function updateRole(ModifyRoleRequest $request, Role $role)
    {
        try {
            $role = Role::find($role->id);
            $role->name = $request->name;
            $role->name_slug = $request->name_slug;
            $role->description = $request->description;
            $role->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Rol modificado correctamente',
                    'data' => ['role' => $role],
                ],
                200,
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    public function searchRole($request)
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'A')
                ->where('name', 'ILIKE', $request . '%')
                ->paginate(10);
            //echo($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'roles' => $roles,
                    ],
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }
}
