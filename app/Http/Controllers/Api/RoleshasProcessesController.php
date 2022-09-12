<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RoleHasProceces\RoleHasProcesCreateRequest;
use App\Models\RolesHasProcess;
use Exception;

class RoleshasProcessesController extends Controller
{

    /**
     * It returns all the rolehasprocesses in the database.
     */
    public function index()
    {
        try {
            $rolehasprocesses = RolesHasProcess::All();
            response()->json([
                'rolehasprocesses' => $rolehasprocesses
            ], 200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

    public function create(RoleHasProcesCreateRequest $request)
    {
        try {
            $rolehasprocesses = new RolesHasProcess();
            $rolehasprocesses->$request->role_id;
            $rolehasprocesses->$request->process_id;
            $rolehasprocesses->save();
            response()->json([
                "success" => true,
                "message" => "RoleHasProcesses created successfull",
                "data" => ["role" => $rolehasprocesses], 200
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
