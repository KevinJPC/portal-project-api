<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordController extends Controller
{

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
                ]);
            } else {
                return response([
                    'message' => 'La contraseña actual no es la correcta'
                ], 401);
            }
        } catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
