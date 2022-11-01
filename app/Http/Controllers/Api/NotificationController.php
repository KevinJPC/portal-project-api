<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getNotifications($request)
    {
        try {
            $user_notification = DB::table('processes')
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
                ->orderBy('users_has_processes.se_oid', 'desc');
            $user_notification_oid = $user_notification->pluck('se_oid');

            $notify = $this->sqlsrv_connection
                ->table('wfstruct')
                ->select('wfstruct.nmstruct', 'wfstruct.dsstruct')
                ->join('wfprocess')
                ->on('wfprocess.cdprocess', '=', ' wfstruct.idprocess')
                ->where('wfstruct.fgstatus', '=', 2)
                ->where('wfstruct.fgtype', '=', 2)
                ->whereIn('wfstruct.idprocess', $user_processes_oid)
                ->orderBy('wfprocess.idprocess', 'desc')
                ->get();

            foreach ($notify as $key => $notify) {
                $notify->dsstruct = json_decode($notify->dsstruct);
                $user_notification[$key] = (object) array_merge(
                    (array) $user_notification[$key],
                    (array) [
                        'activity' => $notify->nmstruct,
                        'procces' =>
                            $workflow->dsstruct?->nom ?? $workflow->nmstruct,
                    ],
                );
            }

            /**
             * select wfstruct.nmstruct, wfstruct.dsstruct from wfprocess
             * join wfstruct on wfprocess.cdprocess = wfstruct.idprocess
             * where wfprocess.fgstatus = 1
             * and wfstruct.idprocess = 137
             * and wfstruct.fgstatus = 2
             * and wfstruct.fgtype = 2
             * order by wfstruct.idprocess desc
             */

            /* $notification = new Notification();
             * $notification = $request->description;
             * $notification = $request->users_has_process_id;
             * $notification->save();*/

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Nueva notificación',
                    'data' => ['notification' => $user_notification],
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