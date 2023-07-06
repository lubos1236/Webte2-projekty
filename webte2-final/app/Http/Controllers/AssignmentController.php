<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentGroup;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class AssignmentController extends Controller
{
  public function create(CreateAssignmentRequest $request): JsonResponse
  {
    $this->authorize('create', AssignmentGroup::class);

    $validated = $request->validated();
    $user = auth()->user();
    $student_ids = $validated['student_ids'] ?? User::where('role', 'student')->pluck('id')->toArray();

    $assignment_group = AssignmentGroup::create([
      'teacher_id' => $user->id,
      'title' => $validated['title'],
      'description' => $validated['description'],
      'start_date' => isset($validated['start_date']) ? str_replace('Z', '', $validated['start_date']) : now()->utc(),
      'end_date' => isset($validated['end_date']) ? str_replace('Z', '', $validated['end_date']) : null,
      'max_points' => $validated['max_points'],
    ]);

    $max_points_per_assignment = $validated['max_points'] / count($validated['exercise_set_ids']);
    foreach ($validated['exercise_set_ids'] as $exercise_set_id) {
      $assignment = Assignment::create([
        'assignment_group_id' => $assignment_group->id,
        'exercise_set_id' => $exercise_set_id,
        'max_points' => $max_points_per_assignment,
      ]);

      foreach ($student_ids as $student_id) {
        $assignment->submissions()->create([
          'student_id' => $student_id,
        ]);
      }
    }

    return response()->json($assignment_group);
  }

  public function index(): array
  {
    $student = auth()->user();

    return [
      'current' => AssignmentGroup::with('assignments.submissions', 'teacher:id,first_name,last_name')
        ->where('start_date', '<', now()->utc())
        ->where(function (Builder $q) {
          $q->where('end_date', '>', now()->utc())
            ->orWhereNull('end_date');
        })
        ->whereHas('assignments.submissions', function (Builder $q) use ($student) {
          $q->where('student_id', $student->id)
            ->whereNull('provided_solution');
        })
        ->get(),

      'past' => AssignmentGroup::with('assignments.submissions', 'teacher:id,first_name,last_name')
        ->where(function (Builder $q) use ($student) {
          $q->where('end_date', '<', now()->utc())
            ->orWhere(function (Builder $q) use ($student) {
              $q->whereHas('assignments.submissions', function (Builder $q) use ($student) {
                $q->where('student_id', $student->id)
                  ->whereNotNull('provided_solution');
              })->whereDoesntHave('assignments.submissions', function (Builder $q) use ($student) {
                $q->where('student_id', $student->id)
                  ->whereNull('provided_solution');
              });
            });
        })
        ->whereHas('assignments.submissions', function (Builder $q) use ($student) {
          $q->where('student_id', $student->id);
        })
        ->get()
    ];
  }

  public function show(string $id)
  {
    $assignmentGroup = AssignmentGroup::with([
      'assignments' => function (Builder $q) {
        $q->with([
          'submissions' => function (Builder $q) {
            $q->where('student_id', auth()->user()->id);
          },
        ]);
      }])
      ->where('id', $id)
      ->where('start_date', '<', now()->utc())
      ->firstOrFail();

    $teacher = auth()->user()->isTeacher();
    $to_load = 'assignments.submissions.exercise:id,task';
    if ($teacher || ($assignmentGroup->end_date && $assignmentGroup->end_date < now()->utc())) {
      $to_load .= ',solution';
    }

    $assignmentGroup->load($to_load);

    return $assignmentGroup;
  }
}
