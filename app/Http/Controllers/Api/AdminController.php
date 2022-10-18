<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\RegisterAdminRequest;

class AdminController extends Controller
{
    /**
     * It creates a new user with the role of admin.
     *
     * @param RegisterAdminRequest request The request object.
     *
     * @return A JSON object
     */
    public function registerAdmin(RegisterAdminRequest $request)
    {
        try {
            $role = DB::table('roles')
                ->select('id')
                ->where('name_slug', '=', 'admin')
                ->first();

            $user = User::create([
                'name' => $request->name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'dni' => $request->dni,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'state' => 'A',
                'role_id' => $role->id,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Registro exitoso',
                    'data' => [
                        'user' => $user,
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
     * It updates the user's data in the database
     *
     * @param User user The user object that is being updated.
     * @param UpdateUserRequest request The request object.
     *
     * @return A JSON response with the success of the operation and a message.
     */
    public function updateAdmin(User $user, UpdateAdminRequest $request)
    {
        try {
            if (User::where('id', $user->id)->exists()) {
                User::where('id', $user->id)->update([
                    'name' => $request->name,
                    'first_last_name' => $request->first_last_name,
                    'second_last_name' => $request->second_last_name,
                    'email' => $request->email,
                ]);

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Usuario modificado correctamente',
                    ],
                    200,
                );
            }
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
     * It gets all the active users from the database, except the current user, and paginates them
     *
     * @return A list of active users.
     */
    public function getActiveAdmins()
    {
        try {
            $active_users = DB::table('users')
                ->select(
                    'users.id',
                    'users.name',
                    'users.dni',
                    'users.first_last_name',
                    'users.second_last_name',
                    'users.email',
                    'users.created_at',
                    'users.updated_at',
                )
                ->where('users.id', '!=', Auth::user()->id)
                ->join('roles', function ($join) {
                    $join
                        ->on('users.role_id', '=', 'roles.id')
                        ->where('roles.name_slug', '=', 'admin');
                })
                ->where('users.state', '=', 'A')
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'active_users' => $active_users,
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
     * It gets all the inactive users from the database, and returns them in a paginated list
     *
     * @return A list of inactive users.
     */
    public function getInactiveAdmins()
    {
        try {
            $inactive_users = DB::table('users')
                ->select(
                    'users.id',
                    'users.name',
                    'users.dni',
                    'users.first_last_name',
                    'users.second_last_name',
                    'users.email',
                    'users.created_at',
                    'users.updated_at',
                )
                ->join('roles', function ($join) {
                    $join
                        ->on('users.role_id', '=', 'roles.id')
                        ->where('roles.name_slug', '=', 'admin');
                })
                ->where('users.state', '=', 'I')
                ->paginate(10);

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'inactive_users' => $inactive_users,
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
     * It activates a user by changing its state to 'I' (inactive)
     *
     * @param User user The user model instance.
     *
     * @return A response with the status code 200 and a message of success.
     */
    public function inactivateAdmin(User $user)
    {
        try {
            User::where('id', $user->id)->update([
                'state' => 'I',
            ]);

            return response(
                [
                    'success' => true,
                    'message' => 'Usuario inactivado correctamente',
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
     * It activates a user by changing its state to 'A' (active)
     *
     * @param User user The user object that is being modified.
     *
     * @return A response with the status code 200 and a message.
     */
    public function activateAdmin(User $user)
    {
        try {
            User::where('id', $user->id)->update([
                'state' => 'A',
            ]);

            return response(
                [
                    'success' => true,
                    'message' => 'Usuario activado correctamente',
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

    public function searchAdmin($request)
    {
        try {
            $search_users = DB::table('users')
                ->select(
                    'users.id',
                    'users.name',
                    'users.dni',
                    'users.first_last_name',
                    'users.second_last_name',
                    'users.email',
                    'users.created_at',
                    'users.updated_at',
                )
                ->where('users.id', '!=', Auth::user()->id)
                ->join('roles', function ($join) {
                    $join
                        ->on('users.role_id', '=', 'roles.id')
                        ->where('roles.name_slug', '=', 'admin');
                })
                ->where('users.state', '=', 'A')
                ->where('users.name', 'ILIKE', $request . '%')
                ->orWhere('users.dni', 'ILIKE', $request . '%')
                ->orWhere('users.email', 'ILIKE', $request . '%')
                ->get();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'search_users' => $search_users,
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