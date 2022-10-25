<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Process;
use App\Models\UsersHasProcess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserHasProcess\StartProcessRequest;

class UserHasProcessController extends Controller
{
    public function startProcess(Process $process, StartProcessRequest $request)
    {
        try {
            $process_started = UsersHasProcess::create([
                'user_id' => Auth::user()->id,
                'process_id' => $process->id,
                'status' => $request->status,
                'activity' => $request->activity,
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

    public function getUserProcesses()
    {
        try {
            $user_processes = DB::table('processes')
                ->select(
                    'processes.id',
                    'processes.name',
                    'processes.created_at',
                    'users_has_processes.activity',
                    'users_has_processes.status',
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
}
