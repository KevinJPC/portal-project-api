<?php

namespace App\Http\Controllers\Api;

use App\Models\Process;
use Illuminate\Http\Request;
use App\Models\UsersHasProcess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\SeSuiteController;
use App\Http\Requests\UserHasProcess\StartProcessRequest;
use App\Http\Requests\UserHasProcess\SaveUserProcessEnabledActivityFormRequest;

class UserHasProcessController extends Controller
{
    private $workflow_web_services;
    private $forms_web_services;
    private $sqlsrv_connection;

    /**
     * The constructor function is called when the class is instantiated. It is used to initialize the
     * class properties.
     */
    public function __construct()
    {
        $this->workflow_web_services = app('workflow_web_services');
        $this->forms_web_services = app('forms_web_services');
        $this->sqlsrv_connection = DB::connection('sqlsrv');
    }

    /**
     * It creates a new workflow on SE Ssuite using web service workflow, and then creates a new record in the database
     * with the workflow's ID
     *
     * @param Process process the process to be started
     */
    public function startProcess(Process $process)
    {
        try {
            /* Calling the `newWorkflow` function from the `workflow_web_services` object. */
            $workflow = $this->workflow_web_services->newWorkflow([
                'ProcessID' => $process->se_oid,
                'WorkflowTitle' =>
                    $process->se_name .
                    ' Portal ' .
                    Auth::user()->dni .
                    ' ' .
                    date('d-m-Y'),
            ]);

            $process_started = UsersHasProcess::create([
                'user_id' => Auth::user()->id,
                'process_id' => $process->id,
                'se_oid' => $workflow->RecordKey,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Proceso iniciado exitosamente',
                    'data' => [
                        'process_started' => $process_started,
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
     * It gets the user's processes from the database and then gets the status, enabled activity, started date and finish date of each process from se suite
     * database
     *
     * @return A response with the data and a 200 status code.
     */
    public function getUserProcesses()
    {
        try {
            /* A query builder that is selecting the user's processes*/
            $user_processes = DB::table('processes')
                ->select(
                    'processes.id',
                    'processes.name',
                    'users_has_processes.se_oid',
                )
                ->join('users_has_processes', function ($join) {
                    $join
                        ->on(
                            'processes.id',
                            '=',
                            'users_has_processes.process_id',
                        )
                        ->where(
                            'users_has_processes.user_id',
                            '=',
                            Auth::user()->id,
                        );
                })
                ->orderBy('users_has_processes.se_oid', 'desc')
                ->paginate(10);

            /* Getting an array of `se_oid` column from the `user_processes` collection. */
            $user_processes_oid = $user_processes->pluck('se_oid');

            $workflow_struct = $this->sqlsrv_connection
                ->table('wfstruct')
                ->select('idprocess', 'nmstruct', 'dsstruct')
                ->where('wfstruct.fgtype', '=', 2)
                ->where('wfstruct.fgstatus', '=', 2);

            /* Getting the status, enabled activity, started date and finish date of each process from SE Suite
             database that matches one of the oids passed in the user_process_oid array. */
            $workflows = $this->sqlsrv_connection
                ->table('wfprocess')
                ->select(
                    'wfprocess.cdprocess',
                    'wfprocess.fgstatus',
                    'wfstruct.nmstruct',
                    'wfstruct.dsstruct',
                    'wfprocess.dtstart',
                    'wfprocess.dtfinish',
                )
                ->leftJoinSub($workflow_struct, 'wfstruct', function ($join) {
                    $join->on('wfprocess.cdprocess', '=', 'wfstruct.idprocess');
                })
                ->whereIn('wfprocess.cdprocess', $user_processes_oid)
                ->orderBy('wfprocess.cdprocess', 'desc')
                ->get();

            /* A foreach loop that is iterating over the workflows */
            foreach ($workflows as $key => $workflow) {
                /*
                 * Decoding the json string that is stored in the `dsstruct` column of the `wfstruct`
                 table. Gets the values previusly configured on SE Suite for each enabled activity
                 */
                $workflow->dsstruct = json_decode($workflow->dsstruct);

                /* Merging the `user_processes` object with a new object that has the process status, enabled activity,
                 started date and finished date from workflow object. */
                $user_processes[$key] = (object) array_merge(
                    (array) $user_processes[$key],
                    (array) [
                        'status' => $workflow->fgstatus,
                        'enabled_activity' =>
                            $workflow->dsstruct?->nom ?? $workflow->nmstruct,
                        'started_at' => $workflow->dtstart,
                        'finished_at' => $workflow->dtfinish,
                    ],
                );
            }

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
     * Gets the information of a user process from the database and from the SE Suite database.
     *
     * @param UsersHasProcess
     *
     * @return A response with the data and a 200 status code.
     */
    public function getUserProcessById(UsersHasProcess $usershasprocess)
    {
        try {
            /* Getting the process name from the database. */
            $process = DB::table('processes')
                ->select('name')
                ->where('id', '=', $usershasprocess->process_id)
                ->first();

            /* Getting the workflow information from the SE Suite database. */
            $sesuite_workflow = $this->sqlsrv_connection
                ->table('wfprocess')
                ->select(
                    'wfprocess.dtstart',
                    'wfprocess.dtfinish',
                    'wfprocess.fgstatus',
                )
                ->where('wfprocess.cdprocess', '=', $usershasprocess->se_oid)
                ->first();

            /* Getting the workflow's activities from the SE Suite database. */
            $sesuite_workflow_struct = $this->sqlsrv_connection
                ->table('wfstruct')
                ->select(
                    'wfstruct.nrorder',
                    'wfstruct.idstruct',
                    'wfstruct.nmstruct',
                    'wfstruct.dsstruct',
                    'wfstruct.fgstatus',
                )
                ->where('wfstruct.idprocess', '=', $usershasprocess->se_oid)
                ->where('wfstruct.fgtype', '=', 2)
                ->orderBy('wfstruct.nrorder', 'asc')
                ->get();

            $activities = [];

            /* Iterating over the workflow's activities*/
            foreach ($sesuite_workflow_struct as $key => $struct) {
                /*
                 * Decoding the json string that is stored in the `dsstruct` column of the `wfstruct`
                 * table. Gets the values previusly configured on SE Suite for each activity.
                 */
                $struct->dsstruct = json_decode($struct->dsstruct);

                /* Creating an array of activities. */
                $activities[$key] = [
                    'se_oid' => $struct->idstruct,
                    'order' => $struct->nrorder,
                    'name' => $struct->dsstruct?->nom ?? $struct->nmstruct,
                    'execute_on_portal' =>
                        $struct->dsstruct?->ejecportal ?? false,
                    'status' => $struct->fgstatus,
                ];

                /* Getting the order of the enabled activity. */
                if ($struct->fgstatus === '2') {
                    $enabled_activity_order = $struct->nrorder;
                }
            }

            /* Calculating the percentage of advance of the process. */
            $percentage_advance = round(
                ($enabled_activity_order - 1) / (sizeOf($activities) + 1),
                2,
            );

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'process' => [
                            'se_oid' => $usershasprocess->se_oid,
                            'name' => $process->name,
                            'started_at' => $sesuite_workflow->dtstart,
                            'finished_at' => $sesuite_workflow->dtfinish,
                            'status' => $sesuite_workflow->fgstatus,
                            'percentage_advance' => $percentage_advance,
                            'activities' => $activities,
                        ],
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

    public function getUserProcessEnabledActivityForm(
        UsersHasProcess $usershasprocess,
    ) {
        $activity = $this->sqlsrv_connection
            ->table('wfprocess')
            ->select(
                'wfstruct.nmstruct',
                'wfstruct.dsstruct',
                'efrevisionform.oid as oidrevisionform',
            )
            ->join('wfstruct', 'wfprocess.cdprocess', '=', 'wfstruct.idprocess')
            ->join(
                'wfactivity',
                'wfstruct.idobject',
                '=',
                'wfactivity.idobject',
            )
            ->join(
                'gnactivity',
                'wfactivity.cdgenactivity',
                '=',
                'gnactivity.cdgenactivity',
            )
            ->join('gnassoc', 'gnactivity.cdassoc', '=', 'gnassoc.cdassoc')
            ->join('gnassocform', 'gnassoc.cdassoc', '=', 'gnassocform.cdassoc')
            ->join('efform', 'gnassocform.oidform', '=', 'efform.oid')
            ->join(
                'efrevisionform',
                'efform.oid',
                '=',
                'efrevisionform.oidform',
            )
            ->where('wfstruct.idprocess', '=', $usershasprocess->se_oid)
            ->where('wfstruct.fgtype', '=', 2)
            ->where('wfstruct.fgstatus', '=', 2)
            ->first();

        $activity->dsstruct = json_decode($activity->dsstruct);

        $fields = $this->sqlsrv_connection
            ->table('efstructform')
            ->select(
                'efstructform.nmlabel',
                'efstructform.idstruct',
                'efstructform.nmvalue',
                'efstructform.fgtype',
                'efstructform.fgrequired',
                'efstructform.fgenabled',
                'efstructform.nrorder',
                'efstructform.fgtype',
                'emattrmodel.idname',
                'emattrmodel.fgtypeattribute',
            )
            ->leftJoin(
                'emattrmodel',
                'efstructform.oidattributemodel',
                '=',
                'emattrmodel.oid',
            )
            ->where(
                'efstructform.oidrevisionform',
                '=',
                $activity->oidrevisionform,
            )
            ->where('efstructform.fghidden', '=', 2)
            ->orderBy('efstructform.nrorder', 'asc')
            ->get();

        $portal_fields = [];

        foreach ($fields as $key => $field) {
            $field->idstruct = explode('x', $field->idstruct);

            if (
                $field->idstruct[0] === 'portal' &&
                $field->idstruct[1] !== 'hj'
            ) {
                $portal_fields[] = $field;
            }
        }

        foreach ($portal_fields as $parent_key => $parent) {
            if (($parent->idstruct[2] ?? null) === 'fs') {
                $children_array = [];
                foreach ($fields as $children_key => $children) {
                    if (
                        ($children->idstruct[1] ?? null) === 'hj' &&
                        ($children->idstruct[2] ?? null) ===
                            ($parent->idstruct[3] ?? null)
                    ) {
                        $children_array[] = $children;
                    }
                }
                $portal_fields[$parent_key] = array_merge(
                    (array) $parent,
                    (array) ['children' => $children_array],
                );
            }
        }

        dd($portal_fields);

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'activity' => [
                        'name' =>
                            $activity->dsstruct?->nom ?? $struct->nmstruct,
                        'form' => $fields,
                    ],
                ],
            ],
            200,
        );
    }

    public function saveUserProcessEnabledActivityForm(
        UsersHasProcess $usershasprocess,
        SaveUserProcessEnabledActivityFormRequest $request,
    ) {
        dd('save form');
    }

    // $response = $this->forms_web_services->__getFunctions();
}

// $response = $this->forms_web_services->getTableRecord([
//     'TableID' => 'tbprueba001',
//     'Pagination' => 1,
//     'TableFieldList' => [
//         'TableField' => [
//             'TableFieldID' => 'OID',
//             'TableFieldValue' => 'fc57792d99be60ded2f472871f59313c',
//         ],
//     ],
// ]);
// dd($response);
