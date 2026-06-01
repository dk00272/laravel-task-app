<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
        'status' => ['nullable', 'in:pending,in_progress,completed'],
        'priority' => ['nullable', 'in:low,medium,high'],
        'sort_by' => ['nullable', 'in:due_date,created_at'],
        'sort_direction' => ['nullable', 'in:asc,desc'],
    ];
    }
}
