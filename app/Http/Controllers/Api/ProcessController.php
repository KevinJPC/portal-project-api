<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Process\RegisterProcessRequest;
use App\Http\Requests\Process\UpdateProcessRequest;

class ProcessController extends Controller
{

    public function registerProcess(RegisterProcessRequest $request){
        try {
            
            $process = Process::create([
                'se_oid' => $request->se_oid,
                'se_name' => $request->se_name,
                'name' => $request->name,
                'visible' => $request->visible,
                'state' => 'A'
            ]);

            return response()->json(
                [
                  'success' => true,
                  'message' => 'Proceso regitrado con éxito',
                  'data' => [
                    'process' => $process,
                  ],
                ],
                200
              );

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }   

    public function updateProcess(Process $process, UpdateProcessRequest $request){
        try {

            if(Process::where('id', $process->id)->exists()){
        
                Process::where('id', $process->id)
                ->update([
                    'name' => $request->name,
                    'visible' => $request->visible,
                ]);
    
                return response()->json(
                    [
                      'success' => true,
                      'message' => 'Proceso modificado correctamente',
                    ],
                    200
                  );
            }

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function getActiveProcesses(){
        try {

            $active_processes = DB::table('processes')
            ->where('state', '=', 'A')            
            ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'active_processes' => $active_processes
                ],
            ],200);
            
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function getInactiveProcesses(){
        try {

            $active_processes = DB::table('processes')
            ->where('state', '=', 'I')            
            ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'active_processes' => $active_processes
                ],
            ],200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function inactivateProcess(Process $process){
        try {            

            Process::where('id', $process->id)
            ->update([
                'state' => 'I'
            ]);

            return response([
                'success' => true,
                'message' => 'Proceso inactivado correctamente'
            ],200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function activateProcess(Process $process){
        try {

            Process::where('id', $process->id)
            ->update([
                'state' => 'A'
            ]);

            return response([
                'success' => true,
                'message' => 'Proceso activado correctamente'
            ],200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
