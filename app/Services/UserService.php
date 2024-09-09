<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    /**
     * Summary of listAllUsers
     * @param int $perPage
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return mixed
     */
    public function listAllUsers(int $perPage)
    {
        try {

            if (Auth::user()->role !== 'Admin') {
                throw new AuthorizationException("Access denied. Only admin can list users.");
            }

            $cacheKey = 'users_' . md5($perPage . request('page', 1));


            $users = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($perPage) {
                $userQuery = User::query();
                $userQuery->select('name', 'email');
                return $userQuery->paginate($perPage);
            });

            return $users;

        } catch (AuthorizationException $e) {

            return response()->json(['error' => $e->getMessage()], 403);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to list users.'], 500);
        }
    }
    /**
     * Summary of createUser
     * @param array $data
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return mixed|User|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse
     */
    public function createUser(array $data)
    {
        try {

            if (Auth::user()->role !== 'Admin') {
                throw new AuthorizationException("Access denied. Only admin can create users.");
            }

            DB::beginTransaction();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            DB::commit();
            return $user;

        } catch (AuthorizationException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->errors()], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Database error occurred while creating user.'], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    /**
     * Summary of getUser
     * @param \App\Models\User $user
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return mixed|User|\Illuminate\Http\JsonResponse
     */
    public function getUser(User $user)
    {
        try {

            if (Auth::user()->role !== 'Admin') {
                throw new AuthorizationException("Access denied. Only admin can view users.");
            }

            return $user;
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get user.'], 500);
        }
    }

    /**
     * Summary of updateUser
     * @param array $data
     * @param \App\Models\User $user
     * @return mixed|User|\Illuminate\Http\JsonResponse
     */
    public function updateUser(array $data,User $user)
    {
        try {

            $user->update(array_filter($data));
            return $user;
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error occurred while updating user.'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    /**
     * Summary of deleteUser
     * @param \App\Models\User $user
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deleteUser(User $user)
    {
        try {

            if (Auth::user()->role !== 'Admin') {
                throw new AuthorizationException("Access denied. Only admin can delete users.");
            }

            $user->delete();
            return response()->json(['message' => 'User deleted successfully.'], 200);

        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error occurred while deleting user.'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }

    }

    /**
     * Summary of retrieveUsers
     * @param int $id
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function retrieveUsers(int $id)
    {
        if (Auth::user()->role !== 'Admin') {
            throw new AuthorizationException("Access denied. Only admin can delete users.");
        }
        try{
            $deletedUser = User::onlyTrashed()->find($id);
            $deletedUser->restore();

        }catch (AuthorizationException $e) {
            // Handle unauthorized access
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);

        } catch (ModelNotFoundException $e) {
            // Handle case where user is not found
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);

        }catch (Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
