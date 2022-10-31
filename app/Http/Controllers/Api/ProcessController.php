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
    private $workflow_web_services;

    /**
     * The constructor function is called when the class is instantiated. It is used to initialize the
     * class properties.
     */
    public function __construct()
    {
        $this->workflow_web_services = app('workflow_web_services');
    }

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
     * It gets a process by its id and returns the process and the roles that are associated with it
     *
     * @param Process process is the process object
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
     * It gets all the processes from the SE Suite database, then filters them by the ones that have a specific
     * property in the json field, then returns the filtered processes
     *
     * @return An array of objects.
     */
    public function getSeSuiteProcesses()
    {
        try {
            /* Getting all the processes from the se suite database. */
            $sesuite_processes = DB::connection('sqlsrv')
                ->table('pmactivity')
                ->select(
                    'pmactivity.idactivity',
                    'pmactivity.nmactivity',
                    'pmactivity.txactivity',
                )
                ->rightJoin(
                    'pmacttype',
                    'pmactivity.cdacttype',
                    '=',
                    'pmacttype.cdacttype',
                )
                ->where(function ($query) {
                    $query
                        ->whereIn('pmactivity.fgstatus', [1, 2])
                        ->orWhereNull('pmactivity.fgstatus');
                })
                ->where('pmacttype.fgtype', '=', '1')
                ->whereNotNull('pmactivity.idactivity')
                ->get();

            $processes_configured = [];

            /* Looping through the SE Suite processes */
            foreach ($sesuite_processes as $key => $process) {
                /*
                 * Decoding the json string that is stored in the txactivity field of the pmactivity
                 table. 
                 */
                $process->txactivity = json_decode($process->txactivity);

                /* Checking if the txactivity field is set and if it has a portal property. If it does, it is adding
                 the idactivity and nmactivity to the array. */
                if ($process->txactivity?->portal ?? false) {
                    $processes_configured[] = [
                        'se_oid' => $process->idactivity,
                        'se_name' => $process->nmactivity,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $processes_configured,
            ]);
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

    /* It searches for a process in the database and returns the results in a paginated format
     *
     * @return An array of objects.
     */
    public function searchProcess($request)
    {
        try {
            //echo($request);
            $search_processes = DB::table('processes')
                ->where('state', '=', 'A')
                ->where('processes.name', 'ILIKE', $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request)
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'search_processes' => $search_processes,
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

    public function getSearchVisiblesProcesses($request)
    {
        try {
            $search_user_processes = DB::table('processes')
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
                ->where('processes.name', 'ILIKE', $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request . '%')
                ->orwhere('processes.name', 'ILIKE', '%' . $request)
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'search_user_processes' => $search_user_processes,
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
