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
    }

    /**
     * It gets the user's processes from the database and then gets the status, enabled activity, started date and finish date of each process from se suite
     * database
     *
     * @return A response with the data and a 200 status code.
     */
    public function getUserProcesses()
    {
        /* A query builder that is selecting the user's processes*/
        $user_processes = DB::table('processes')
            ->select(
                'users_has_processes.id',
                'processes.name',
                'users_has_processes.se_oid',
            )
            ->join('users_has_processes', function ($join) {
                $join
                    ->on('processes.id', '=', 'users_has_processes.process_id')
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
                    'status' => (int) $workflow->fgstatus,
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
                'execute_on_portal' => $struct->dsstruct?->ejecportal ?? false,
                'status' => (int) $struct->fgstatus,
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
                        'status' => (int) $sesuite_workflow->fgstatus,
                        'percentage_advance' => $percentage_advance,
                        'activities' => $activities,
                    ],
                ],
            ],
            200,
        );
    }

    /**
     * It gets the form fields from a database and returns them as a JSON response
     *
     * @param UsersHasProcess usershasprocess is the process that the user is currently in
     */
    public function getUserProcessEnabledActivityForm(
        UsersHasProcess $usershasprocess,
    ) {
        //Getting the enabled activity of the SE Suite process (workflow)
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

        /* Decoding the json activity description into an object. */
        $activity->dsstruct = json_decode($activity->dsstruct);

        /* Getting form fields from SE Suite based on oidrevisionform from the activity */
        $fields = $this->sqlsrv_connection
            ->table('efstructform')
            ->select(
                'efstructform.nmlabel as label_text',
                'efstructform.idstruct as identifier',
                'efstructform.nmvalue as value',
                'efstructform.fgtype as component_type',
                'emattrmodel.idname as attribute_name',
                'emattrmodel.fgtypeattribute as attribute_type',
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
            ->get();

        /* Splitting the fields identifier into an array. */
        $fields = $this->splitIdentifier($fields);

        /* Getting the portal fields from the fields array. */
        $portal_fields = $this->getPortalFields($fields);

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'activity' => [
                        'name' =>
                            $activity->dsstruct?->nom ?? $struct->nmstruct,
                        'form' => $portal_fields,
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
        return response()->json(
            [
                'success' => true,
                'message' => 'This functionality is not yet defined',
            ],
            200,
        );
    }

    /**
     * It takes a collection of objects, and for each object, it splits the identifier property into an
     * array, and returns the collection of objects
     */
    private function splitIdentifier($fields)
    {
        return array_map(function ($field) {
            $field->identifier = explode('x', $field->identifier);
            return $field;
        }, $fields->toArray());
    }

    /* Getting the portal fields array based on the format defined in the portal documentation */
    private function getPortalFields($fields)
    {
        /* Creation of an empty array for the elements intended to be displayed in the portal  */
        $portal_fields = [];

        /* Taking the array of fields and adding the fields, targeted to be displayed in the
         *  portal, to the "portal_fields" array based on the format defined in the portal documentation
         */
        foreach ($fields as $field_key => $field) {
            /* Checking if the field is a portal field and a fieldset. */
            if (
                ($field->identifier[0] ?? null) === 'portal' &&
                ($field->identifier[2] ?? null) === 'fs'
            ) {
                /* if the field is a portal field and a fieldset, then the array is traversed
                 *  again in order to find the options of that fieldset */
                $options_array = [];
                foreach ($fields as $field_option_key => $field_option) {
                    if (
                        ($field_option->identifier[0] ?? null) === 'portal' &&
                        ($field_option->identifier[1] ?? null) === 'opc' &&
                        ($field_option->identifier[2] ?? null) ===
                            ($field->identifier[3] ?? null)
                    ) {
                        /* Merging in order to create a new better object for the option field. */
                        $field_option = (object) array_merge(
                            (array) $field_option,
                            [
                                'order' => $field_option->identifier[3],
                            ],
                        );
                        unset($field_option->identifier);
                        $options_array[] = $field_option;
                    }
                }
                /* Sorting the options_array by the value of the key 'order' in ascending order. */
                array_multisort(
                    array_column($options_array, 'order'),
                    SORT_ASC,
                    $options_array,
                );

                /* Merging in order to create a new better object for the fieldset. */
                $field = (object) array_merge(
                    (array) $field,
                    (array) [
                        'attribute_name' =>
                            ($field->identifier[4] ?? null) === 'rb'
                                ? $options_array[0]->attribute_name ?? null
                                : null,
                        'attribute_type' =>
                            ($field->identifier[4] ?? null) === 'rb'
                                ? $options_array[0]->attribute_type ?? null
                                : null,
                        'options_type' => $field->identifier[4],
                        'options' => $options_array,
                    ],
                );
            }

            /* Checking if the field is a portal field and is not a option */
            if (
                ($field->identifier[0] ?? null) === 'portal' &&
                ($field->identifier[1] ?? null) !== 'opc'
            ) {
                /* Merging in order to create a new better object for the fieldset. */
                $portal_fields[] = (object) array_merge(
                    (array) $field,
                    (array) [
                        'order' => $field->identifier[1],
                    ],
                );
            }

            /* Unsetting the identifier field from the portal fields array. */
            unset($portal_fields[$field_key]->identifier);
        }

        /* Sorting the portal_fields by the value of the key 'order' in ascending order. */
        array_multisort(
            array_column($portal_fields, 'order'),
            SORT_ASC,
            $portal_fields,
        );

        return $portal_fields;
    }
}
