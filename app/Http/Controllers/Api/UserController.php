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
        if (User::where('id', Auth::user()->id)->exists()) {
            User::where('id', Auth::user()->id)->update([
                'name' => $request->name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'email' => $request->email,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Información modificada correctamente',
                ],
                200,
            );
        }
    }

    /**
     * It checks if the old password is correct, if it is, it updates the password with the new one
     *
     * @param UpdatePasswordRequest request The request object.
     *
     * @return A response with a success message
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $current_user = Auth::user();

        if (Hash::check($request->old_password, $current_user->password)) {
            if (Hash::check($request->password, $current_user->password)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'La nueva contraseña debe ser diferente a la actual',
                    ],
                    400,
                );
            } else {
                $user_id = $current_user->id;
                $new_password = Hash::make($request->password);

                User::where('id', $user_id)->update([
                    'password' => $new_password,
                ]);

                return response(
                    [
                        'success' => true,
                        'message' => 'La contraseña se modificó correctamente',
                    ],
                    200,
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'La contraseña actual no es la correcta',
                ],
                400,
            );
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
    public function getUserById(User $user)
    {
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'first_last_name' => $user->first_last_name,
                        'second_last_name' => $user->second_last_name,
                        'email' => $user->email,
                    ],
                ],
            ],
            200,
        );
    }
}
