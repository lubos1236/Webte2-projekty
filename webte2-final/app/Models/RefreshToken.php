<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
  protected $fillable = [
    'refresh_token',
    'expires_at',
    'user_id',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
