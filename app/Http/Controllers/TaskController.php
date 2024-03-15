<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequestCreate;
use App\Http\Requests\TaskRequestUpdate;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $tasks = Task::where('user_id', '=', auth()->user()->id)->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequestCreate $taskRequestCreate)
    {
        $data = $taskRequestCreate->validated();

        $error = $this->ValidateTaskOfUser($data["user_id"]);

        if ($error) {
            $error = new \Exception('Unauthorized to create task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        $task = Task::create($data);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResource
    {
        $error = $this->ValidateTaskOfUser($task->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to get this task', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequestUpdate $taskRequestUpdate, Task $task): JsonResource
    {
        $data = $taskRequestUpdate->validated();

        $error = $this->ValidateTaskOfUser($task->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to update task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        $task->update($data);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): Response|JsonResource
    {
        $error = $this->ValidateTaskOfUser($task->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to delete task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        $task->state = 0;

        $task->save();

        return response()->noContent();
    }

    public function ValidateTaskOfUser(int $user_id): bool
    {
        //todo: ONLY ADMIN CAN TOUCH ALL TASKS
        if ($user_id !== auth()->user()->id) {
            return true;
        }

        return false;
    }
}
