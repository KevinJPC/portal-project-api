<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * It updates the user's information in the database
     * 
     * @param UpdateUserRequest request The request object.
     * 
     * @return a response with the message "Usuario modificado correctamente"
     */
    public function updateUser(UpdateUserRequest $request)
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
                ],200);
            }
        } catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * It checks if the old password is correct, if it is, it updates the password with the new one
     * 
     * @param UpdatePasswordRequest request The request object.
     * 
     * @return A response with a message.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $current_user = Auth::user();

        try {

            if (Hash::check($request->old_password, $current_user->password)) {
                $email = $current_user->email;
                $new_password = Hash::make($request->new_password);

                DB::table('users')->where('email', $email)->update(['password' => $new_password]);

                return response([
                    'message' => 'La contraseña se modificó correctamente'
                ],200);
            } else {
                return response([
                    'message' => 'La contraseña actual no es la correcta'
                ], 400);
            }
        } catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
