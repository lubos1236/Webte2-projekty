<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseSet extends Model
{
  protected $fillable = [
    'file_path',
    'hash',
  ];

  public function exercises(): HasMany
  {
    return $this->hasMany(Exercise::class);
  }
}
