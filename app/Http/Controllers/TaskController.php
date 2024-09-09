<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\EditStatusRequest;
use App\Http\Requests\TaskUpdateRequest;

class TaskController extends Controller
{


    protected $taskService;
    /**
     * Summary of __construct
     * @param \App\Services\TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {

        $this->taskService = $taskService;
    }
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['priority','status']);
        $perPage = $request->input('per_page', 15);
        $tasks = $this->taskService->listAllTask($filters,$perPage);
        return ApiResponseService::paginated($tasks,'tasks retrive success');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\TaskStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskStoreRequest $request)
    {
      $data = $request->validated();
      $task = $this->taskService->createTask($data);
      return ApiResponseService::success($task,'task created success');
    }

    /**
     * Summary of show
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        $this->taskService->getTask($task);
        return ApiResponseService::success($task,'task retrive success');
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\TaskUpdateRequest $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $data = $request->validated();
        $task = $this->taskService->updateTask($data,$task);
        return ApiResponseService::success($task,'task update success');
    }

    /**
     * Summary of destroy
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
      return $this->taskService->deleteTask($task);
    }
    /**
     * Summary of assigne
     * @param \App\Http\Requests\AssignTaskRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assigne(AssignTaskRequest $request,int $id)
    {
        $data = $request->validated();
        $this->taskService->assigneTask($data,$id);
        return ApiResponseService::success(null,'task assigened success',201);

    }
    /**
     * Summary of editTaskStatus
     * @param \App\Http\Requests\EditStatusRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editTaskStatus(EditStatusRequest $request,int $id)
    {
        $data =$request->validated();
        $this->taskService->editStatus($data,$id);
        return ApiResponseService::success(null,'status updated success',201);

    }
    /**
     * Summary of retrieve
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve(int $id)
    {
        $this->taskService->retrievetask($id);
        return ApiResponseService::success(null,'task restore success',201);
    }
}
