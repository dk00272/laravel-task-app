<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\TaskFilterRequest;

use App\Models\Task;

use App\Http\Resources\TaskResource;


class TaskController extends Controller
{
    public function __construct()
    {
        //$this->authorizeResource(Task::class, 'task');
    }

    public function index(TaskFilterRequest $request)
    {
        return TaskResource::collection(
            $request->user()
                ->tasks()
                ->latest()
                ->paginate()
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()
            ->tasks()
            ->create($request->validated());

        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(
        UpdateTaskRequest $request,
        Task $task
    ) {
        $task->update(
            $request->validated()
        );

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted'
        ]);
    }


public function summary(Request $request)
{
    $user = $request->user();

    $baseQuery = Task::query()
        ->where('user_id', $user->id);

    // total tasks
    $totalTasks = (clone $baseQuery)->count();

    // status breakdown (SQL aggregation)
    $statusCounts = (clone $baseQuery)
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status');

    // overdue tasks (DB-level filtering)
    $overdue = (clone $baseQuery)
        ->whereNotNull('due_date')
        ->where('due_date', '<', now())
        ->where('status', '!=', 'completed')
        ->count();

    return response()->json([
        'total_tasks' => $totalTasks,
        'by_status' => [
            'todo' => $statusCounts['pending'] ?? 0,
            'inprogress' => $statusCounts['in_progress'] ?? 0,
            'done' => $statusCounts['completed'] ?? 0,
        ],
        'overdue' => $overdue,
    ]);
}
}