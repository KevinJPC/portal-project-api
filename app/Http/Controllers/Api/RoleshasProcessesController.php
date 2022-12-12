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
        $rolehasprocesses = DB::table('roles_has_processes')
            ->latest()
            ->paginate(10);
        return response()->json(
            [
                'rolehasprocesses' => $rolehasprocesses,
            ],
            200,
        );
    }

    /**
     * It creates a new rolehasprocesses object and saves it to the database
     *
     * @param array [ids]
     * @param idProcess
     *
     * @return The response is a JSON object with the following structure:
     */
    public function createRolehasProcesses($array, $idProcess)
    {
        $roles = [];
        foreach ($array as $role_key => $role_id) {
            $roles[] = [
                'process_id' => $idProcess,
                'role_id' => $role_id,
            ];
        }

        RolesHasProcess::insert($roles);

        return response()->json(
            [
                'success' => true,
                'message' => 'Roles creados correctamente',
                'data' => ['RoleHasProcesses' => $roles],
            ],
            200,
        );
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
        $roles = DB::table('roles')
            ->select('roles.id', 'roles.name')
            ->join('roles_has_processes', function ($join) {
                $join->on('roles.id', '=', 'roles_has_processes.role_id');
            })
            ->where('roles_has_processes.process_id', '=', $id)
            ->get();

        return $roles;
    }

    /**
     * Modify the roles that are linked to the process, they are eliminated
     * if they were removed or new ones are added
     *
     * @param array [ids]
     * @param idProcess
     *
     * @return JSON response with the data of the rolehasprocesses object.
     */
    public function modifyRolehasProcesses($array, $idProcess)
    {
        $arraynow = DB::table('roles_has_processes')
            ->where('process_id', '=', $idProcess)
            ->groupBy('role_id')
            ->pluck('role_id');
        $arraynow = $arraynow->toArray();
        for ($i = 0; $i < count($array); $i++) {
            /* Checking if the role_id is in the array of roles that the process has, if it is not,
             it is adding it to the process. */
            if (!in_array($array[$i], $arraynow)) {
                $rolehasprocesses = new RolesHasProcess();
                $rolehasprocesses->role_id = $array[$i];
                $rolehasprocesses->process_id = $idProcess;
                $rolehasprocesses->save();
            }
        }
        for ($i = 0; $i < count($arraynow); $i++) {
            /* Deleting the roles that are in the array that the process has but are not in the
             array that I have. */
            if (!in_array($arraynow[$i], $array)) {
                RolesHasProcess::where('role_id', $arraynow[$i])->delete();
            }
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Roles modificados correctamente',
                'data' => ['RoleHasProcesses' => $rolehasprocesses],
            ],
            200,
        );
    }
}
