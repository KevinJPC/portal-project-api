<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
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

        $token = $user->createToken('auth_token')->plainTextToken;

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
    }

    /**
     * It checks if the user is authenticated and if the user's state is 'A' (active). If it is, it
     * creates a token and returns a response with the user and the token.
     *
     * @param LoginRequest request The request object.
     *
     * @return The user and the token.
     */
    public function login(LoginRequest $request)
    {
        $isAuthenticated = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $user = Auth::user();

        if (!$isAuthenticated || $user->state !== 'A') {
            abort(401, 'Correo electrónico o contraseña incorrecta.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'success' => true,
                'message' => 'Sesión iniciada exitosamente',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ],
            200,
        );
    }

    /**
     * It revokes the token of the user that is currently logged in.
     *
     * @param Request request The request object.
     */
    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json(
            [
                'success' => true,
                'message' => 'Sesión cerrada exitosamente',
            ],
            200,
        );
    }

    /**
     * It returns the user's data and current access token
     *
     * @param Request request The request object.
     *
     * @return The user's data
     */
    public function reconnect(Request $request)
    {
        $user = Auth::user();
        $token = explode(' ', $request->header('Authorization'))[1];
        return response()->json(
            [
                'success' => true,
                'message' => 'Reconexión exitosa',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ],
            200,
        );
    }
}
