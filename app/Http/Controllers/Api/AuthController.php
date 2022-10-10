<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class AuthController extends Controller
{
    /**
     * It creates a new user in the database.
     *
     * @param RegisterAuthRequest request The request object.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'dni' => $request->dni,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'state' => 'A',
                'role_id' => $request->role_id,
            ]);

            $token = $user->createToken('auth_token')->accessToken;

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Registro exitoso',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
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

    /**
     * The function receives a LoginAuthRequest object, which is a request object that contains the
     * email and password fields. If the email and password are correct, the function returns a
     * response with the user and token data. If the email and password are incorrect, the function
     * returns a response with an error message
     *
     * @param LoginAuthRequest request The request object.
     */
    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(
                    [
                        'success' => false,
                        'test_camel_case' => 'funca?',
                        'message' =>
                            'Correo electrónico o contraseña incorrecta',
                    ],
                    401,
                );
            }
            $user = Auth::user();
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Sesión iniciada exitosamente',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'first_last_name' => $user->first_last_name,
                            'second_last_name' => $user->second_last_name,
                            'role' => $user->role->name_slug,
                        ],
                        'token' => $token,
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

    /**
     * It revokes the token of the user that is currently logged in.
     *
     * @param Request request The request object.
     */
    public function logout(Request $request)
    {
        try {
            Auth::user()
                ->token()
                ->revoke();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Sesión cerrada exitosamente',
                ],
                200,
            );
        } catch (\Expection $exception) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                400,
            );
        }
    }

    /**
     * It returns the user's data and the token
     *
     * @param Request request The request object.
     *
     * @return The user's data and the token.
     */
    public function reconnect(Request $request)
    {
        try {
            $user = Auth::user();
            $token = explode(' ', $request->header('Authorization'))[1];
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reconexión exitosa',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'first_last_name' => $user->first_last_name,
                            'second_last_name' => $user->second_last_name,
                            'role' => $user->role->name_slug,
                        ],
                        'token' => $token,
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
