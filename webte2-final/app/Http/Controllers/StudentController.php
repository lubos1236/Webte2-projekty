<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
  public function index(Request $request)
  {
    $qParams = $request->query();
    $acceptHeader = $request->getAcceptableContentTypes();

    $page = $qParams["page"] ?? 1;
    $pageSize = $qParams["size"] ?? 10;
    $sort = $qParams["sort"] ?? "id";
    $order = $qParams["order"] ?? "asc";
    $submissionDetails = $qParams["submissionDetails"] ?? false;

    if ($order !== "asc" && $order !== "desc") {
      $order = "asc";
    }

    $result = $this->getStudents([
      "page" => $page,
      "pageSize" => $pageSize,
      "sort" => $sort,
      "order" => $order,
      "submissionDetails" => $submissionDetails
    ]);

    if (in_array("text/csv", $acceptHeader)) {
      $csv = $this->studentsToCsv($result["items"], $submissionDetails);

      return response($csv, 200)
        ->header("Content-Type", "text/csv")
        ->header("Content-Disposition", "attachment; filename=students.csv");
    }

    return response()->json($result, 200);
  }


  public function show(string $id): JsonResponse
  {
    $student = User::where("role", "student")
      ->select("id", "first_name", "last_name", "email")
      ->find($id);

    if (!$student) {
      return response()->json(["message" => "Student not found."], 404);
    }

    $this->authorize("view", $student);

    $assignmentGroups = DB::table('assignment_groups')
      ->join('assignments', 'assignment_groups.id', '=', 'assignments.assignment_group_id')
      ->join('submissions', 'assignments.id', '=', 'submissions.assignment_id')
      ->join('exercise_sets', 'assignments.exercise_set_id', '=', 'exercise_sets.id')
      ->where('submissions.student_id', '=', $id)
      ->groupBy('assignment_groups.id')
      ->select(
        'assignment_groups.id',
        'assignment_groups.title',
        'assignment_groups.description',
        'assignment_groups.start_date',
        'assignment_groups.end_date',
        'assignment_groups.max_points',
        'assignment_groups.created_at',
        DB::raw('JSON_ARRAYAGG(JSON_OBJECT("id", assignments.id, "max_points", assignments.max_points, "points", submissions.points, "exercise_id", submissions.exercise_id, "filename", exercise_sets.file_path)) as assignments'))
      ->get();

    foreach ($assignmentGroups as $assignmentGroup) {
      $assignmentGroup->assignments = json_decode($assignmentGroup->assignments);

      foreach ($assignmentGroup->assignments as $assignment) {
        $assignment->filename = basename($assignment->filename);
      }
    }

    $result = [
      "student" => $student,
      "assignmentGroups" => [
        "items" => $assignmentGroups,
        "total" => count($assignmentGroups)
      ],
    ];

    return response()->json($result, 200);
  }


  private function getStudents(array $params)
  {
    if ($params["submissionDetails"]) {
      $students = $this->getStudentsWithSubmissionDetails($params);

      return [
        "items" => $students->items(),
        "total" => $students->total(),
      ];
    }

    $students = User::where("role", "student")
      ->select("id", "first_name", "last_name", "email")
      ->get();

    return [
      "items" => $students,
      "total" => count($students),
    ];
  }


  private function getStudentsWithSubmissionDetails(array &$params)
  {
    $studentsQuery = User::where("role", "student")
      ->select("id", "first_name", "last_name", "email")
      ->withCount([
        "submissions as submissions_count" => function ($query) {
          $query->whereNotNull("exercise_id");
        },
        "submissions as submissions_count_provided_solution" => function ($query) {
          $query->whereNotNull("provided_solution");
        },
        "submissions as submissions_points_sum" => function ($query) {
          $query->select(DB::raw("COALESCE(sum(points), 0) as submissions_points_sum"))->whereNotNull("provided_solution");
        },
      ])
      ->orderBy($params["sort"], $params["order"]);

    $numericColumns = ["id", "submissions_count", "submissions_count_provided_solution", "submissions_points_sum"];

    if (in_array($params["sort"], $numericColumns)) {
      $studentsQuery = $studentsQuery->orderBy("last_name", $params["order"]);
    }

    return $studentsQuery
      ->paginate($params["pageSize"], ["*"], "page", $params["page"]);
  }


  private function studentsToCsv(&$students, bool $submissionDetails)
  {
    $csv = "id,first_name,last_name,email";

    if ($submissionDetails) {
      $csv .= ",submissions_count,submissions_count_provided_solution,submissions_points_sum";
    }

    $csv .= "\n";

    foreach ($students as $student) {
      $csv .= "{$student->id},{$student->first_name},{$student->last_name},{$student->email}";

      if ($submissionDetails) {
        $csv .= ",{$student->submissions_count},{$student->submissions_count_provided_solution},{$student->submissions_points_sum}";
      }

      $csv .= "\n";
    }

    return $csv;
  }
}
