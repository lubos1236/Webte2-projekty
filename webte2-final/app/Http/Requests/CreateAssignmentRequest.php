<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'title' => ['required', 'string'],
      'description' => ['required', 'string'],
      'student_ids.*' => ['required', 'exists:users,id'],
      'exercise_set_ids' => ['required', 'array'],
      'exercise_set_ids.*' => ['required', 'exists:exercise_sets,id'],
      'max_points' => ['required', 'numeric', 'min:0.01'],
      'start_date' => ['nullable', 'date'],
      'end_date' => ['nullable', 'date', 'after:start_date'],
      'student_ids' => ['nullable', 'array'],
    ];
  }
}
