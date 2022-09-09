<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditUserController extends Controller
{

    /**
     * It updates the user's information in the database
     * 
     * @param UpdateUserRequest request The request object.
     * 
     * @return a response with the message "Usuario modificado correctamente"
     */
    public function editUser(UpdateUserRequest $request)
    {
        $current_user = Auth::user();
        $user_id = $current_user->id;

        try {
            if (User::where('id', $user_id)->exists()) {
                $new_name = $request->name;
                $new_first_last_name = $request->first_last_name;
                $new_second_last_name = $request->second_last_name;
                $new_email = $request->email;

                DB::table('users')->where('id', $user_id)->update([
                    'name' => $new_name,
                    'first_last_name' => $new_first_last_name,
                    'second_last_name' => $new_second_last_name,
                    'email' => $new_email,
                ]);

                return response([
                    'message' => 'Usuario modificado correctamente'
                ]);
            }
        } catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
