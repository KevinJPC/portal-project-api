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

            response()->json([
                "success" => true,
                "message" => "Role created successfull",
                "data" => ["role" => $role], 200
            ]);
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
    public function getAllRoles()
    {
        try {
            $role = Role::all();
            response()->json([
                'role' => $role
            ], 200);
        } catch (Exception $exception) {
            response()->json([
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
            $roles = DB::table('users')->where('role_id', $role->id)->get();

            if (empty($roles)) {
                Role::where('id', $role->id)
                    ->update([
                        'state' => 'I'
                    ]);
                $role->save();
                response()->json([
                    "success" => true,
                    "message" => "Rol Desactivado correctamente",
                    "data" => ["role" => $role]
                ], 200);
            } else {
                response()->json([
                    "success" => false,
                    "message" => "Existen uniones entre este rol y algún usuario",
                ], 400);
            }
        } catch (Exception $exception) {
            response()->json([
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
            Role::where('id', $role->id)
                ->update([
                    'state' => 'A'
                ]);
            $role->save();
            response()->json([
                "success" => true,
                "message" => "Rol Activado correctamente",
                "data" => ["role" => $role]
            ], 200);
        } catch (Exception $exception) {
            response()->json([
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
            response()->json(["role" => $role], 200);
        } catch (Exception $exception) {
            response()->json([
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

            response()->json([
                "success" => true,
                "message" => "Role modified successfull",
                "data" => ["role" => $role]
            ], 200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }
}
