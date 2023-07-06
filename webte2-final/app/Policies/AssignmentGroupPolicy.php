<?php

namespace App\Policies;

use App\Models\User;

class AssignmentGroupPolicy
{
  public function before(User $user, string $ability): ?bool
  {
    if ($user->role === 'teacher' || $user->role === 'admin') {
      return true;
    }
    return null;
  }

  public function create(User $user): bool
  {
    return false;
  }
}
