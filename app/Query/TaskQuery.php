<?php

namespace App\Query;

class TaskQuery
{
    /**
     * Create a new class instance.
     */
     public function __construct(
        protected Request $request
    ) {}

    public function index(Request $request)
    {
    $query = $request->user()
        ->tasks()
        ->latest();

    $tasks = (new TaskQuery($request))
        ->apply($query)
        ->paginate(10)
        ->withQueryString();

    return TaskResource::collection($tasks);
    }

    public function apply($query)
    {
        return $query
            ->when(
                $this->request->status,
                fn ($q) =>
                    $q->where('status', $this->request->status)
            )
            ->when(
                $this->request->priority,
                fn ($q) =>
                    $q->where('priority', $this->request->priority)
            )
            ->when(
                $this->request->sort_by,
                fn ($q) => $this->applySorting($q)
            );
    }

    protected function applySorting($query)
    {
        return match ($this->request->sort_by) {

            'due_date' => $query->orderBy(
                'due_date',
                $this->direction()
            ),

            'created_at' => $query->orderBy(
                'created_at',
                $this->direction()
            ),

            default => $query->latest(),
        };
    }

    protected function direction(): string
    {
        return $this->request->sort_direction === 'asc'
            ? 'asc'
            : 'desc';
    }
}
