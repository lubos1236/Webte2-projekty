<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentGroup extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'description',
    'max_points',
    'teacher_id',
    'start_date',
    'end_date',
  ];

  public function assignments(): HasMany
  {
    return $this->hasMany(Assignment::class);
  }

  public function teacher(): BelongsTo
  {
    return $this->belongsTo(User::class, 'teacher_id');
  }
}
