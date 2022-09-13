<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Http\Requests\Auth\RegisterAuthRequest;

class AuthController extends Controller
{

    /**
     * It creates a new user in the database.
     * 
     * @param RegisterAuthRequest request The request object.
     */
    public function register(RegisterAuthRequest $request) {

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
            200
          );

        } catch (\Exception $exception) {
            return response()->json([
                'success'=> false,
                'message' => $exception->getMessage(),
            ], 400);
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
    public function login(LoginAuthRequest $request)
    {

    try{

        if (!Auth::attempt($request->only('email', 'password'))) {
 
            return response()->json([
                'success' => false, 
                'message' => 'Correo electrónico o contraseña incorrecta',
                ], 201);
        }

        $token = Auth::user()->createToken('auth_token')->plainTextToken;
 
        return response()->json([
            'success' => true, 
            'message' => 'Sesión iniciada exitosamente',
            'data' => [
                'user' => Auth::user(),
                'token' => $token,
            ]
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'success'=> false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function logout(Request $request) {
        
    }
}
