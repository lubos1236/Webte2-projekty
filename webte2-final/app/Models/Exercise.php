<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
  protected $fillable = [
    'task',
    'solution',
  ];

  protected $hidden = [
    'exercise_set_id',
  ];

  function exercise_set(): BelongsTo
  {
    return $this->belongsTo(ExerciseSet::class);
  }
}
