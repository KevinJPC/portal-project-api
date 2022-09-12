<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\ModifyRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Role\RoleRequest;

class RoleController extends Controller
{
    /**
     * Index para mostrar
     * store para guardar
     * update para actualizar
     * destroy para eliminar
     * edit para mostrar
     */

    public function Create(CreateRoleRequest $request)
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

    public function index(Request $request)
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


    public function destroy(Role $role, $request)
    {

        try {
            $roles = DB::table('role')->where('role_id', $role->id);

            if (empty($roles)) {
                Role::where('id', $role->id)
                    ->update([
                        'state' => 'I'
                    ]);
                $role->state = $request->state;
                $role->save();
                response()->json([
                    "success" => true,
                    "message" => "Rol eliminado correctamente",
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


    public function validationRoleUser($role)
    {
    }






    public function get($id)
    {
        try {
            $role = Role::find($id);
            response()->json(["role" => $role], 200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

    public function update(ModifyRoleRequest $request, $id)
    {

        try {
            $role = Role::find($id);
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
