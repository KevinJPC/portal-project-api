<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Process;
use Illuminate\Support\Arr;
use App\Models\Role;
use App\Models\RolesHasProcess;
use App\Http\Requests\RoleHasProceces\RoleHasProcesCreateRequest;

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
                ->paginate(10);
            return response()->json(
                [
                    'rolehasprocesses' => $rolehasprocesses,
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
            for ($i = 0; $i <= count($array); $i++) {
                $rolehasprocesses = new RolesHasProcess();
                $rolehasprocesses->role_id = $array[$i];
                $rolehasprocesses->process_id = $idProcess;
                $rolehasprocesses->save();
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Roles creados correctamente',
                    'data' => ['RoleHasProcesses' => $rolehasprocesses],
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
     * It returns a list of roles that have a specific process id.
     *
     * @param id the id of the process
     *
     * @return A collection of roles that have a process_id of .
     */
    public function getRoleHasProcesses($id)
    {
        try {
            $roles = DB::table('roles')
                ->select('roles.id', 'roles.name')
                ->join('roles_has_processes', function ($join) {
                    $join->on('roles.id', '=', 'roles_has_processes.role_id');
                })
                ->where('roles_has_processes.process_id', '=', $id)
                ->get();

            return $roles;
        } catch (Exception $exception) {
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
            ->where('process_id','=',$idProcess)->groupBy('role_id')
            ->pluck('role_id');
            $arraynow = $arraynow -> toArray();
            for ($i=0; $i < count($array); $i++) { 
                if (!in_array($array[$i], $arraynow)) {
                    $rolehasprocesses = new RolesHasProcess();
                    $rolehasprocesses->role_id = $array[$i];
                    $rolehasprocesses->process_id = $idProcess;
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
            response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }
}