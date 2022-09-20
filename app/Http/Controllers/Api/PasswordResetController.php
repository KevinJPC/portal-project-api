<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\PasswordReset\ResetPasswordRequest;
use App\Http\Requests\PasswordReset\ValidateTokenRequest;
use App\Http\Requests\PasswordReset\ForgotPasswordRequest;

class PasswordResetController extends Controller
{
    /**
     * It sends a reset link to the user's email address
     * 
     * @param ForgotPasswordRequest request The request object.
     * 
     * @return A JSON response with a success boolean and a message string.
     */
    public function forgotPassword(ForgotPasswordRequest $request){
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status === Password::RESET_LINK_SENT){
            return response()->json([
                'success' => true,
                'message' => __($status),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 400);
    }

    /**
     * It takes a request, validates it, and then resets the password
     * 
     * @param ResetPasswordRequest request The request object.
     */
    public function resetPassword(ResetPasswordRequest $request){

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->tokens()->delete();
     
                event(new PasswordReset($user));
            }
        );

        if($status === Password::PASSWORD_RESET){
            return response()->json([
                'success' => true,
                'message' => __($status)
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => [__($status)]
        ], 400);
    }
}
