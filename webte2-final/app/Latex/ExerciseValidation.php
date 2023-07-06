<?php

namespace App\Latex;

use App\Models\Submission;
use App\Models\Exercise;

class ExerciseValidation
{
  public static function validate(string $expected, string | null $actual): bool
  {
    if (!$actual) {
      return false;
    }

    $result = exec("python ../scripts/validate.py \"$expected\" \"$actual\"");

    if ($result === 'True') {
      return true;
    }

    return false;
  }
}
