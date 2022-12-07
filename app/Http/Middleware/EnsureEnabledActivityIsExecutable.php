<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEnabledActivityIsExecutable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //1 query to get the enabled activity of the process, like this:

        // $this->activity = DB::connection('sqlsrv')
        // ->table('wfprocess')
        // ->select('wfstruct.idobject', 'wfstruct.dsstruct')
        // ->join('wfstruct', 'wfprocess.cdprocess', '=', 'wfstruct.idprocess')
        // ->where(
        //     'wfstruct.idprocess',
        //     '=',
        //     request()->route('usershasprocess')->se_oid,
        // )
        // ->where('wfstruct.fgtype', '=', 2)
        // ->where('wfstruct.fgstatus', '=', 2)
        // ->first();

        //2 conditional, in order to know if the current enabled activity is executable based on dsstruct, like this:

        // if (!json_decode($this->activity->dsstruct)?->ejecportal) {
        //example error message
        //     return abort(404, 'Actualmente, no parace haber una actividad habilidad para este proceso');
        // }

        //3 if it is, pass the enabled activity in the request in order to use this in the controller or next middlwares

        //4 return next
        return $next($request);
    }
}
