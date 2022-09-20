<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RoleHasProceces\RoleHasProcesCreateRequest;
use App\Models\RolesHasProcess;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Process;
use Illuminate\Support\Arr;

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

    /**
     * I'm trying to create a new row in the table "role_has_processes" for each element in the array
     * 
     * 
     * @param array 
     * @param idProcess 
     * 
     * @return The response is a JSON object
     */
    public function createRolehasProcesses($array, $idProcess)
    {
        try {

           for ($i=0; $i <= count($array); $i++) {
                $rolehasprocesses = new RolesHasProcess();
                $rolehasprocesses->role_id=$array[$i];
                $rolehasprocesses->process_id=$idProcess;
                $rolehasprocesses->save();
           }
            
            return response()->json([
                "success" => true,
                "message" => "Roles creados correctamente",
                "data" => ["RoleHasProcesses" => $rolehasprocesses]
            ],200);
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }

   /**
    * It gets all the roles that have access to a specific process
    * 
    * @param Process process The process object that is being passed in.
    * 
    * @return a json response.
    */
    public function getRoleHasProcesses(Process $process)
    {
        try {
            $rolehasprocesses = DB::table('roles_has_processes')
            ->where('process_id','=',$process->id)
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

   
    /**
     * I want to compare the array that I receive with the array that I get from the database, and if
     * there is a difference, I want to add or delete the elements that are not in the database
     * 
     * @param array [1,2,3]
     * @param idProcess The id of the process
     * 
     * @return The response is a JSON object with the following structure:
     */
    public function modifyRolehasProcesses($array, $idProcess)
    {
        try {
            
            $arraynow = DB::table('roles_has_processes')
            ->where('process_id','=',$idProcess)->groupBy('role_id')
            ->pluck('role_id');
            $arraynow = $arraynow -> toArray();
            for ($i=0; $i < count($array); $i++) { 
                if (!in_array($array[$i], $arraynow)) {
                    $rolehasprocesses = new RolesHasProcess();
                    $rolehasprocesses->role_id=$array[$i];
                    $rolehasprocesses->process_id=$idProcess;
                    $rolehasprocesses->save();
                }
            }
            for ($i=0; $i < count($arraynow); $i++) { 
                if (!in_array($arraynow[$i],$array)) {
                    RolesHasProcess::where('role_id', $arraynow[$i])->delete();
                } 
            }
                return response()->json([
                    "success" => true,
                    "message" => "Roles Modificados correctamente",
                    "data" => ["RoleHasProcesses" => $rolehasprocesses]
            ],200);            
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }
}
