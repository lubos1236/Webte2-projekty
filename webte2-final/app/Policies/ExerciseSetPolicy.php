<?php

namespace App\Policies;

use App\Models\ExerciseSet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExerciseSetPolicy
{
  public function before(User $user, $ability): ?bool
  {
    if ($user->role === 'admin' || $user->role === 'teacher') {
      return true;
    }
    return null;
  }

  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return false;
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, ExerciseSet $exerciseSet): bool
  {
    return false;
  }
}
