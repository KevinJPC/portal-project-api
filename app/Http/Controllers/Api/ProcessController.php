<?php

namespace App\Http\Controllers\Api;

use App\Models\Process;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Process\UpdateProcessRequest;
use App\Http\Requests\Process\RegisterProcessRequest;
use App\Http\Controllers\Api\RoleshasProcessesController;

class ProcessController extends Controller
{
    /**
     * It creates a new process and create a new instance of the RoleshasProcessesController
     * and calling the createRolehasProcesses method
     *
     * @param RegisterProcessRequest request The request object.
     */
    public function registerProcess(RegisterProcessRequest $request)
    {
        try {
            $process = Process::create([
                'se_oid' => $request->se_oid,
                'se_name' => $request->se_name,
                'name' => $request->name,
                'visible' => $request->visible,
                'state' => 'A',
            ]);

            $role_has_processes = new RoleshasProcessesController();
            $role_has_processes->createRolehasProcesses(
                $request->roles,
                $process->id,
            );

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Proceso regitrado con éxito',
                    'data' => [
                        'process' => $process,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    public function updateProcess(
        Process $process,
        UpdateProcessRequest $request,
    ) {
        try {
            if (Process::where('id', $process->id)->exists()) {
                Process::where('id', $process->id)->update([
                    'name' => $request->name,
                    'visible' => $request->visible,
                ]);

                $role_has_processes = new RoleshasProcessesController();
                $role_has_processes->modifyRolehasProcesses(
                    $request->roles,
                    $process->id,
                );

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Proceso modificado correctamente',
                    ],
                    200,
                );
            }
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It gets a process by its id
     *
     * @param Process process The process object that contains the id of the process you want to get.
     *
     * @return A JSON object with the process data.
     */
    public function getProcessById(Process $process)
    {
        try {
            $role_has_processes_controller = new RoleshasProcessesController();
            $roles = $role_has_processes_controller->getRoleHasProcesses(
                $process->id,
            );

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'process' => $process,
                        'roles' => $roles,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It returns a paginated list of all active processes
     *
     * @return A list of active processes
     */
    public function getActiveProcesses()
    {
        try {
            $active_processes = DB::table('processes')
                ->where('state', '=', 'A')
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'active_processes' => $active_processes,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It returns a paginated list of inactive processes
     *
     * @return A list of inactive processes
     */
    public function getInactiveProcesses()
    {
        try {
            $inactive_processes = DB::table('processes')
                ->where('state', '=', 'I')
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'inactive_processes' => $inactive_processes,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It gets all the processes that are visible and that the user has access to
     *
     * @return A list of processes that are visible to the user.
     */
    public function getVisiblesProcesses()
    {
        try {
            $user_processes = DB::table('processes')
                ->join('roles_has_processes', function ($join) {
                    $join
                        ->on(
                            'roles_has_processes.process_id',
                            '=',
                            'processes.id',
                        )
                        ->where(
                            'roles_has_processes.role_id',
                            '=',
                            Auth::user()->role_id,
                        );
                })
                ->where('processes.visible', '=', 1)
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'user_processes' => $user_processes,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It updates the state of a process to 'I' (inactive)
     *
     * @param Process process The process to be inactivated
     *
     * @return A response with a success message and a 200 status code.
     */
    public function inactivateProcess(Process $process)
    {
        try {
            Process::where('id', $process->id)->update([
                'state' => 'I',
            ]);

            return response(
                [
                    'success' => true,
                    'message' => 'Proceso inactivado correctamente',
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It updates the state of a process to 'A' (active)
     *
     * @param Process process The process to be inactivated
     *
     * @return A response with a success message and a 200 status code.
     */
    public function activateProcess(Process $process)
    {
        try {
            Process::where('id', $process->id)->update([
                'state' => 'A',
            ]);

            return response(
                [
                    'success' => true,
                    'message' => 'Proceso activado correctamente',
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It searches for a process in the database and returns the results in a paginated format
     *
     * @return An array of objects.
     */
    public function searchProcess($request)
    {
        try {
            //echo($request);
            $active_processes = DB::table('processes')
                ->where('state', '=', 'A')
                ->where('processes.name', 'ILIKE', $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request)
                ->get()
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'active_processes' => $active_processes,
                    ],
                ],
                200,
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }
}