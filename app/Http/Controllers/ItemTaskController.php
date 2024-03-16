<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemTaskCreateRequest;
use App\Http\Requests\ItemTaskListRequest;
use App\Http\Requests\ItemTaskUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ItemTaskResource;
use App\Models\ItemTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ItemTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ItemTaskListRequest $itemTaskListRequest): JsonResource
    {
        $data = $itemTaskListRequest->validated();

        $task = Task::with('itemTasks')->where('user_id', '=', auth()->user()->id)->where('id', '=', $data['task_id'])->first();

        $itemTasks = $task ? $task->itemTasks : [];

        return ItemTaskResource::collection($itemTasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemTaskCreateRequest $itemTaskCreateRequest): JsonResource
    {
        $data = $itemTaskCreateRequest->validated();

        $task = Task::find($data['task_id']);

        $error = TaskController::ValidateTaskOfUser($task->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to create item task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        $data['done'] = 0;

        $itemTask = ItemTask::create($data);

        return new ItemTaskResource($itemTask);
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemTask $itemTask): JsonResource
    {
        $error = TaskController::ValidateTaskOfUser($itemTask->task()->first()->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to get item task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        return new ItemTaskResource($itemTask);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemTaskUpdateRequest $itemTaskUpdateRequest, ItemTask $itemTask)
    {
        $data = $itemTaskUpdateRequest->validated();

        $error = TaskController::ValidateTaskOfUser($itemTask->task()->first()->user_id);

        if ($error) {
            $error = new \Exception('Unauthorized to update item task of another user', Response::HTTP_BAD_REQUEST);

            return new ErrorResource($error);
        }

        $itemTask->update($data);

        return new ItemTaskResource($itemTask);
    }
}
