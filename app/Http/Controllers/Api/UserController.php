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
     * It updates the user's data in the database
     * 
     * @param UpdateUserRequest request The request object.
     * 
     * @return A JSON object with the success status and a message.
     */
    public function updateUser(UpdateUserRequest $request)
    {

        try {
            if (User::where('id', Auth::user()->id)->exists()) {

                User::where('id', Auth::user()->id)
                ->update([
                    'name' => $request->name,
                    'first_last_name' => $request->first_last_name,
                    'second_last_name' => $request->second_last_name,
                    'email' => $request->email,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Usuario modificado correctamente'
                ],200);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }


    /**
     * It checks if the old password is correct, if it is, it updates the password with the new one
     * 
     * @param UpdatePasswordRequest request The request object.
     * 
     * @return A response with a success message and a status code of 200.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $current_user = Auth::user();

        try {

            if (Hash::check($request->old_password, $current_user->password)) {

                $user_id = $current_user->id;
                $new_password = Hash::make($request->password);

                User::where('id', $user_id)
                ->update([
                    'password' => $new_password
                ]);

                return response([
                    'success' => true,
                    'message' => 'La contraseña se modificó correctamente'
                ],200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual no es la correcta'
                ], 400);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * It gets the user's name, first last name, second last name and email from the database 
     * and returns it in a JSON response
     * 
     * @param User user The user object that is passed in the request.
     * 
     * @return The user's name, first last name, second last name and email.
     */
    public function getUserById(User $user){
        try {
            $user = DB::table('users')
            ->select('name', 'first_last_name', 'second_last_name', 'email')
            ->where('id', $user->id)
            ->first();

            return response()->json([
                'success' => true,
                'data' => ['user' => $user],
            ],200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

}
