<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
  protected $fillable = ['student_id', 'assignment_id'];

  public function exercise(): BelongsTo
  {
    return $this->belongsTo(Exercise::class);
  }

  public function assignment(): BelongsTo
  {
    return $this->belongsTo(Assignment::class);
  }

  public function student(): BelongsTo
  {
    return $this->belongsTo(User::class, 'student_id');
  }
}
