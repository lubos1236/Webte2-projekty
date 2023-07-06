<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
  /**
   * Create a new policy instance.
   */
  public function __construct()
  {
    //
  }

  public function before(User $user, string $ability): ?bool
  {
    if ($user->role === 'teacher' || $user->role === 'admin') {
      return true;
    }
    return null;
  }

  public function view(User $user, User $model): bool
  {
    return $user->id === $model->id;
  }
}
