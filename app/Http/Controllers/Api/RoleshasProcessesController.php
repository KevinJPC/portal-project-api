<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Role;
use App\Models\Process;
use App\Models\RolesHasProcess;
use Illuminate\Support\Facades\DB;
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
                ->select('role_id')
                ->where('process_id', '=', $process->id);

            if (count($array) < count($arraynow)) {
                for ($i = count($array); $i < count($arraynow); $i++) {
                    $rolehasprocesses = new RolesHasProcess();
                    $rolehasprocesses->role_id = $array[$i];
                    $rolehasprocesses->process_id = $idProcess;
                    $rolehasprocesses->save();
                }

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Roles agregados correctamente',
                        'data' => ['RoleHasProcesses' => $rolehasprocesses],
                    ],
                    200,
                );
            } else {
                //$arraydelete = [];
                for ($i = 0; $i < count($array); $i++) {
                    for ($j = 0; $j < count($arraynow); $j++) {
                        if ($array[$i] == $arraynow[$j]) {
                            unset($arraynow[$j]);
                        }
                    }
                }

                for ($i = 0; $i < count($arraynow); $i++) {
                    RolesHasProcess::where('role_id', $arraynow[$i])->delete();
                }

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Roles eliminados correctamente',
                        'data' => ['RoleHasProcesses' => $rolehasprocesses],
                    ],
                    200,
                );
            }
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
