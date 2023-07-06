<?php

namespace App\Http\Controllers;

use App\Latex\ExerciseValidation;
use App\Models\Exercise;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
  public function submit(Submission $submission, Request $request): JsonResponse|Submission
  {
    $this->authorize('submit', $submission);

    $validated = $request->validate([
      'solution' => 'required|string',
    ]);

    $submission->load('assignment.assignment_group', 'exercise');

    $assignmentGroup = $submission->assignment->assignment_group;

    if ($assignmentGroup->start_date > now()->utc()) {
      return response()->json(['message' => 'Assignment not found.'], 404);
    }
    if ($submission->provided_solution) {
      return response()->json(['message' => 'Already submitted'], 400);
    }
    if ($assignmentGroup->end_date && $assignmentGroup->end_date < now()->utc()) {
      return response()->json(['message' => 'Assignment has ended'], 400);
    }

    $expected = $submission->exercise->solution;
    $actual = $validated['solution'];
    $correct = ExerciseValidation::validate($expected, $actual);

    $submission->provided_solution = $actual;
    $submission->points = $correct ? $submission->assignment->max_points : 0;
    $submission->save();

    if (!$submission->assignment->end_date || $submission->assignment->end_date > now()->utc()) {
      unset($submission->exercise->solution);
    }

    return $submission;
  }

  public function generate(Submission $submission)
  {
    $this->authorize('generate', $submission);

    $submission->load('assignment', 'assignment.assignment_group');

    $assignmentGroup = $submission->assignment->assignment_group;

    if ($assignmentGroup->end_date && $assignmentGroup->end_date < now()->utc()) {
      return response()->json(['message' => 'Assignment has not ended'], 400);
    }

    if (!$submission->exercise_id) {
      $submission->exercise_id = Exercise::where('exercise_set_id', $submission->assignment->exercise_set_id)
        ->inRandomOrder()
        ->pluck('id')
        ->first();
      $submission->save();
    }

    $to_load = 'exercise:id,task';
    if ($assignmentGroup->end_date && $assignmentGroup->end_date < now()->utc()) {
      $to_load .= ',solution';
    }

    $submission->load($to_load);
    unset($submission->assignment);

    return response()->json($submission);
  }
}
