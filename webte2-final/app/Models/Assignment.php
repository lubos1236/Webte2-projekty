<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
  protected $fillable = [
    'assignment_group_id',
    'exercise_set_id',
    'max_points',
  ];

  public function assignment_group(): BelongsTo
  {
    return $this->belongsTo(AssignmentGroup::class);
  }

  public function exercise_set(): BelongsTo
  {
    return $this->belongsTo(ExerciseSet::class, 'exercise_set_id');
  }

  public function submissions(): HasMany
  {
    return $this->hasMany(Submission::class);
  }
}
