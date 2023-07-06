<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
  public function before(User $user, string $ability): ?bool
  {
    if ($user->role === 'teacher' || $user->role === 'admin') {
      return true;
    }
    return null;
  }

  public function submit(User $user, Submission $submission): bool
  {
    return $user->id === $submission->student_id;
  }

  public function generate(User $user, Submission $submission): bool
  {
    return $user->id === $submission->student_id;
  }
}
