<?php

namespace App\Http\Requests\UserHasProcess;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

/*
    !!!IMPORTANT: This is a custom request just to test the creation of dynamic rules and attributes based on form fields
*/
class SaveUserProcessEnabledActivityFormRequest extends FormRequest
{
    protected $fields;

    public function __construct(Request $request)
    {
        //instead of doing this, you have to modify the middleware EnsureEnabledActivityIsExecutable and pass the activity from the request
        $this->activity = DB::connection('sqlsrv')
            ->table('wfprocess')
            ->select('wfstruct.idobject', 'wfstruct.dsstruct')
            ->join('wfstruct', 'wfprocess.cdprocess', '=', 'wfstruct.idprocess')
            ->where(
                'wfstruct.idprocess',
                '=',
                request()->route('usershasprocess')->se_oid,
            )
            ->where('wfstruct.fgtype', '=', 2)
            ->where('wfstruct.fgstatus', '=', 2)
            ->first();

        $this->fields = DB::connection('sqlsrv')
            ->table('wfactivity')
            ->select(
                'efstructform.nmlabel',
                'emattrmodel.idname',
                'efstructform.fgrequired',
                'gnformatfield.dssimplemask',
                'gnformatfield.dsregularexp',
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
            ->join(
                'efstructform',
                'efrevisionform.oid',
                '=',
                'efstructform.oidrevisionform',
            )
            ->join(
                'emattrmodel',
                'efstructform.oidattributemodel',
                '=',
                'emattrmodel.oid',
            )
            ->leftJoin(
                'gnformatfield',
                'efstructform.cdformatfield',
                '=',
                'gnformatfield.cdformatfield',
            )
            ->where('wfactivity.idobject', '=', $this->activity->idobject)
            ->get();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //this is no longer necessary, yo have to remove this validation and instead use EnsureIsOwnerUser middleware in the route
        if ($this->route('usershasprocess')->user_id !== Auth::user()->id) {
            return false;
        }

        //instead of doing this, you have to modify the EnsureEnabledActivityIsExecutable middleware
        if (!json_decode($this->activity->dsstruct)?->ejecportal) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        //Creation of dynamic rules based on the form of the enabled activity
        $rules = [];

        foreach ($this->fields as $key => $field) {
            //
            $rules[$field->idname] = [];

            if ($field->fgrequired == 1) {
                array_push($rules[$field->idname], 'required');
            }
            if ($field->dsregularexp) {
                array_push(
                    $rules[$field->idname],
                    'regex:' . '/' . $field->dsregularexp . '/',
                );
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        //Creation of dynamic attributes based on the form of the enabled activity
        $attributes = [];
        foreach ($this->fields as $key => $field) {
            $attributes[$field->idname] = strtolower($field->nmlabel);
        }
        return $attributes;
    }
}
