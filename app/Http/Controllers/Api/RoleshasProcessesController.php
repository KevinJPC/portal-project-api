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
    * I have a table called roles_has_processes, which has two columns: role_id and process_id. I want
    * to compare the values of the column role_id with an array that I receive from the frontend, and
    * if the values of the array are less than the values of the column, I want to add the values of
    * the array to the column, and if the values of the array are more than the values of the column, I
    * want to delete the values of the column that are not in the array
    * 
    * @param array the array of roles that the user has selected
    * @param idProcess The id of the process
    * 
    * @return a JSON response.
    */
    public function modifyRolehasProcesses($array, $idProcess)
    {
        try {
            $arraynow = DB::table('roles_has_processes')
            ->select('role_id')
            ->where('process_id','=',$process->id);

            if (count($array) < count($arraynow)) {
                for ($i=count($array); $i < count($arraynow); $i++) { 
                    $rolehasprocesses = new RolesHasProcess();
                    $rolehasprocesses->role_id=$array[$i];
                    $rolehasprocesses->process_id=$idProcess;
                    $rolehasprocesses->save();
                }

                return response()->json([
                    "success" => true,
                    "message" => "Roles agregados correctamente",
                    "data" => ["RoleHasProcesses" => $rolehasprocesses]
            ],200);

            }else{
                //$arraydelete = [];
                for ($i=0; $i < count($array); $i++) { 
                    for ($j=0; $j < count($arraynow); $j++) { 
                        if ($array[$i] == $arraynow[$j]) {
                            unset($arraynow[$j]);
                        }
                    }
                }

                for ($i=0; $i < count($arraynow); $i++) { 
                    RolesHasProcess::where('role_id', $arraynow[$i])->delete();
                }

                return response()->json([
                    "success" => true,
                    "message" => "Roles eliminados correctamente",
                    "data" => ["RoleHasProcesses" => $rolehasprocesses]
            ],200);
            }

           
            
            
        } catch (Exception $exception) {
            response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
            ], 400);
        }
    }
}
