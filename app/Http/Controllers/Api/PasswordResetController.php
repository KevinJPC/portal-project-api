<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return response()->json(
            [
                'success' => true,
                'message' => __(Password::RESET_LINK_SENT),
            ],
            200,
        );
    }

    public function validateResetToken(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Password::tokenExists($user, $request->token)) {
            abort(410, __(Password::INVALID_TOKEN));
        }

        return response()->json(
            [
                'success' => true,
            ],
            200,
        );
    }

    /**
     * It takes a request, validates it, and then resets the password
     *
     * @param ResetPasswordRequest request The request object.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token',
            ),
            function ($user, $password) {
                $user
                    ->forceFill([
                        'password' => Hash::make($password),
                    ])
                    ->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            abort(410, __(Password::INVALID_TOKEN));
        }

        return response()->json(
            [
                'success' => true,
                'message' => __(Password::PASSWORD_RESET),
            ],
            200,
        );
    }
}
