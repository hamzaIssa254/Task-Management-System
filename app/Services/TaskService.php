<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Summary of listAllTask
     * @param array $filters
     * @param int $perPage
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Exception
     * @return mixed
     */
    public function listAllTask(array $filters, int $perPage)
    {
        if (!(Auth::check() && (Auth::user()->role == 'Manager' || Auth::user()->role == 'Admin'))) {

            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'unAuthorized',

            ], 422));
        }



        try {
             // Generate a unique cache key based on filters and pagination
            $cacheKey = 'tasks_' . md5(json_encode($filters) . $perPage . request('page', 1));

            // Check if the cached result exists
            $tasks = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $perPage) {

                $priority = $filters['priority'] ?? null; // Filter by priority
                $status = $filters['status'] ?? null; // Filter by status


                // Use scope Priority and Status from model Task to filter
                return Task::when($priority, function ($query, $priority) {
                    return $query->Priority($priority);
                })
                    ->when($status, function ($query, $status) {
                        return $query->Status($status);
                    })
                    ->paginate($perPage);
            });


            return $tasks;
        } catch (Exception $e) {
            Log::error('error listing tasks ' . $e->getMessage());
            throw new Exception('there is something wrong');
        }
    }
    /**
     * Summary of createTask
     * @param array $data
     * @throws \Exception
     * @return Task|\Illuminate\Database\Eloquent\Model
     */
    public function createTask(array $data)
    {
        DB::beginTransaction();
        try {

            $task = Task::create($data);
            DB::commit();
            return $task;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('error creating tasks ' . $e->getMessage());
            throw new Exception('there is something wrong');
        } catch (ModelNotFoundException $e) {
            Log::error('canno\'t create tasks for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong');
        }
    }
    /**
     * Summary of getTask
     * @param \App\Models\Task $task
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Exception
     * @return Task
     */
    public function getTask(Task $task)
    {
        if (!(Auth::check() && (Auth::user()->role == 'Manager' || Auth::user()->role == 'Admin'))) {

            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'unAuthorized',

            ], 422));
        }
        try {
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('canno\'t retrive this task for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong with retrive this task');
        }
    }
    /**
     * Summary of updateTask
     * @param array $data
     * @param \App\Models\Task $task
     * @throws \Exception
     * @return Task
     */
    public function updateTask(array $data, Task $task)
    {
        try {
            $task->update(array_filter($data));
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('canno\'t update this task for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong with update this task');
        }
    }
    /**
     * Summary of deleteTask
     * @param \App\Models\Task $task
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask(Task $task)
    {
        $user = Auth::user();
        try {
            if ($user->role == 'Manager' || $user->role == 'Admin') {
                $task->delete();
                return ApiResponseService::success('delete task success');
            }
            return ApiResponseService::error('you dont have permission to delete this');
        } catch (ModelNotFoundException $e) {
            Log::error('canno\'t delete this task for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong with delete this task');
        }
    }
    /**
     * Summary of assigneTask
     * @param array $data
     * @param int $id
     * @throws \Exception
     * @return void
     */
    public function assigneTask(array $data, int $id)
    {
        try {
            $task = Task::findOrFail($id);


            $task->update([
                'assigned_to' => $data['assigned_to']
            ]);
            Log::info('Updating Task:', ['task_id' => $task->task_id, 'current_assigned_to' => $task->assigned_to]);
        } catch (ModelNotFoundException $e) {
            Log::error('can\'t assigne this task for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong with assigne this task');
        }
    }
    /**
     * Summary of editStatus
     * @param array $data
     * @param int $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Exception
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function editStatus(array  $data, int $id)
    {
        $task = Task::findOrFail($id);
        if (!(
            Auth::user()->role == 'Manager' ||
            Auth::user()->role == 'Admin' ||
            $task->assigned_to == Auth::id()
        )) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401));
        }
        try {



            $task->update([
                'status' => $data['status']
            ]);
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('canno\'t assigne this task for some reaason ' . $e->getMessage());
            throw new Exception('there is something wrong with assigne this task');
        }
    }
    /**
     * Summary of retrievetask
     * @param int $id
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return void
     */
    public function retrievetask(int $id)
    {
        if (Auth::user()->role !== 'Admin') {
            throw new AuthorizationException("Access denied. Only admin can delete users.");
        }
        try{

            $deletedUser = Task::onlyTrashed()->find($id);
            $deletedUser->restore();
        }catch (AuthorizationException $e) {
            // Handle unauthorized access
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);

        } catch (ModelNotFoundException $e) {
            // Handle case where task is not found
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
