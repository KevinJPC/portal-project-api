<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private $sqlsrv_connection;

    /**
     * The constructor function is called when the class is instantiated. It is used to initialize the
     * class properties.
     */
    public function __construct()
    {
        $this->sqlsrv_connection = DB::connection('sqlsrv');
    }

    public function getNotifications()
    {
        try {
            $user_processes = DB::table('processes')
                ->select(
                    'users_has_processes.id',
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
                ->get();
            //->paginate(10);

            $user_processes_oid = $user_processes->pluck('se_oid');

            $se_processes = $this->sqlsrv_connection
                ->table('wfstruct')
                ->select(
                    'wfstruct.idprocess',
                    'wfstruct.nmstruct',
                    'wfstruct.dsstruct',
                )
                ->join(
                    'wfprocess',
                    'wfprocess.cdprocess',
                    '=',
                    'wfstruct.idprocess',
                )
                ->where('wfprocess.fgstatus', '=', 1)
                ->where('wfstruct.fgstatus', '=', 2)
                ->where('wfstruct.fgtype', '=', 2)
                ->whereIn('wfstruct.idprocess', $user_processes_oid)
                ->orderBy('wfprocess.idprocess', 'desc')
                ->get();
            //dd($se_processes);

            $notifications = [];
            foreach ($se_processes as $key_se => $se_process) {
                $se_process->dsstruct = json_decode($se_process->dsstruct);
                foreach ($user_processes as $key_user => $user_process) {
                    if (
                        $se_process->idprocess === $user_process->se_oid &&
                        $se_process->dsstruct?->ejecportal
                    ) {
                        $notifications[] = [
                            'user_process_id' => $user_process->id,
                            'process_name' => $user_process->name,
                            'activity_name' =>
                                $se_process->dsstruct?->nom ??
                                $se_process->nmstruct,
                        ];
                    }
                }
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Nueva notificación',
                    'data' => ['notifications' => $notifications],
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
}