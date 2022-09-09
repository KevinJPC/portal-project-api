<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Index para mostrar
     * store para guardar
     * update para actualizar
     * destroy para eliminar
     * edit para mostrar
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:5',
                'description',
                'state' => 'required',
            ]);

            $role = new Role();
            $role->name = $request->name;
            $role->description = $request->description;
            $role->state = $request->state;
            $role->save();

            response()->json([
                "success" => true,
                "message" => "Role created successfull",
                "data" => ["role" => $role]
            ]);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ]);
        }
    }

    public function index(Request $request)
    {
        try {
            $role = Role::all();
            response()->json([
                'role'=>$role
            ]);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ]);
        }
    }


    public function destroy($id, $request)
    {

        try {
            $role = Role::find($id);
            $role->state = $request->state;
            $role->save();
            response()->json([
                "success" => true,
                "message" => "Rol eliminado correctamente",
                "data" => ["role" => $role]
            ]);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ]);
        }
    }

    public function get($id)
    {
        try {
            $role = Role::find($id);
            
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|min:5',
                'description',
                'state' => 'required',
            ]);

            $role = Role::find($id);


            $role->name = $request->name;
            $role->description = $request->description;
            $role->state = $request->state;
            $role->save();

            response()->json([
                "success" => true,
                "message" => "Role modified successfull",
                "data" => ["role" => $role]
            ]);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ]);
        }
    }
}
