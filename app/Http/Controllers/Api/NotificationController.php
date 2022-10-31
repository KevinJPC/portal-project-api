<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function createNotification($request)
    {
        try {
            $notification = new Notification();
            $notification = $request->description;
            $notification = $request->users_has_process_id;
            $notification->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Notificación creada correctamente',
                    'data' => ['notification' => $notification],
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

    public function getNotification($request)
    {
        try {
            $notifys = BD::table('notifications')
                ->select(
                    'notifications.id',
                    'notifications.description',
                    'processes.id',
                    'processes.name',
                )
                ->join('users_has_processes', function ($join) {
                    $join
                        ->on(
                            'notifications.users_has_process_id',
                            '=',
                            'users_has_processes.id',
                        )
                        ->where(
                            'users_has_processes.user_id',
                            '=',
                            Auth::user()->id,
                        );
                });
            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'notifys' => $notifys,
                    ],
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
    public function delateNotification($request)
    {
        try {
            DB::table('notification')->delete($request->id);
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Notificación eliminada',
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