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
                ->orderBy('name')
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['roles' => $roles],
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
     * It returns a list of roles that are active and not admin.
     *
     * @return An array of objects.
     */
    public function publicRoles()
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'A')
                ->where('name_slug', '!=', 'admin')
                ->orderBy('name')
                ->get();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['roles' => $roles],
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
     * It returns a paginated list of all roles that are active and not admin.
     *
     * @return A JSON response with the following structure:
     */
    public function getActiveRoles()
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'A')
                ->latest()
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['roles' => $roles],
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
     * If the role has no users assigned to it, then it can be inactivated
     *
     * @param Role role The role to be inactivated
     *
     * @return The response is a JSON object with the following structure:
     */
    public function inactivateRole(Role $role)
    {
        try {
            $hasUsers = DB::table('users')
                ->where('role_id', $role->id)
                ->first();
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
     * It activates a role by changing the state of the role from 'I' to 'A'
     *
     * @param Role
     *
     * @return The response is a JSON object
     */
    public function activateRole(Role $role)
    {
        try {
            $role = Role::find($role->id);
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
            return response()->json(
                ['success' => true, 'data' => ['role' => $role]],
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
            if (!($role->name_slug = $request->name_slug)) {
                $role->name_slug = $request->name_slug;
            }
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

    /**
     * It searches for a role in the database and returns the results in a paginated format
     *
     * @param request the search term
     * @return JSON response
     */
    public function searchRole($request)
    {
        try {
            $roles = DB::table('roles')
                ->where('state', 'A')
                ->where('name', 'ILIKE', $request . '%')
                ->orwhere('name', 'ILIKE', '%' . $request . '%')
                ->orwhere('name', 'ILIKE', '%' . $request)
                ->paginate(10);
            return response()->json(
                ['success' => true, 'data' => ['roles' => $roles]],
                200,
            );
        } catch (\Exception $exception) {
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
