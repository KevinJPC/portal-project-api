<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RoleHasProceces\RoleHasProcesCreateRequest;
use App\Models\RolesHasProcess;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleshasProcessesController extends Controller
{

    /**
     * It returns all the rolehasprocesses in the database.
     */
    public function allRolesHasProcesses()
    {
        try {
            $rolehasprocesses = DB::table('roles_has_processes')
            ->latest()
            ->paginate(10);;
            return response()->json([
                'rolehasprocesses' => $rolehasprocesses
            ], 200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

    public function createRolehasProcesses(RoleHasProcesCreateRequest $request)
    {
        try {
            $rolehasprocesses = new RolesHasProcess();
            foreach ($request as $rolehasprocesses) {
                $rolehasprocesses->role_id=$request->id;
                $rolehasprocesses->process_id=$request->$process_id;
            }
            $rolehasprocesses->save();
            return response()->json([
                "success" => true,
                "message" => "RoleHasProcesses created successfull",
                "data" => ["role" => $rolehasprocesses]
            ],200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }


    public function getRoleHasProcesses(Role $role)
    {
        try {
            $rolehasprocesses = DB::table('roles_has_processes')
            ->where('role_id','=',$role->id)
            ->latest()
            ->paginate(10);
            return response()->json([
                "succeses"=>true,
                "data"=>$rolehasprocesses
            ],200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }



    }
}
