<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\ModifyRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;

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
            $role->description = $request->description;
            $role->state = $request->state;
            $role->save();
            
            return response()->json([
                "success" => true,
                "message" => "Rol creado correctamente",
                "data" => ["role" => $role]
            ],200);

        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

  /**
   * It returns all the roles in the database.
   */
    public function getInactiveRoles()
    {
        try {
            $roles = DB::table('roles')
            ->where('state', 'I')
            ->latest()
            ->paginate(10);
            return response()->json([
                'roles' => $roles
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }


    public function getActiveRoles()
    {
        try {

            $roles = DB::table('roles')
            ->where('state', 'A')
            ->latest()
            ->paginate(10);

            return response()->json([
                'roles' => $roles
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
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
            $hasUsers = DB::table('users')->where('role_id', $role->id)->first();
            //dd($hasUsers);
            if (!$hasUsers) {
                $role -> state='I';
                $role->save();
                return response()->json([
                    "success" => true,
                    "message" => "Rol desactivado correctamente",
                    "data" => ["role" => $role]
                ], 200);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Este rol tiene usuarios asignados",
                ], 400);
            }
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
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
            $role -> state='A';
            $role->save();
            return response()->json([
                "success" => true,
                "message" => "Rol Activado correctamente",
                "data" => ["role" => $role]
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * It gets a role from the database
     * 
     * @param Role role The role object that you want to get.
     */
    public function getRole(Role $role)
    {
        try {
            $role = Role::find($role->id);
            return response()->json(["role" => $role], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
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
            $role->description = $request->description;
            $role->state = $request->state;
            $role->save();

            return response()->json([
                "success" => true,
                "message" => "Rol modificado correctamente",
                "data" => ["role" => $role]
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }
}
