<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
  ];

  public function isTeacher(): bool
  {
    return $this->role === 'teacher' || $this->role === 'admin';
  }

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims(): array
  {
    return [
      'role' => $this->role,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'email' => $this->email,
    ];
  }

  public function assignments(): HasMany
  {
    return $this->hasMany(Assignment::class, 'teacher_id');
  }

  public function submissions(): HasMany
  {
    return $this->hasMany(Submission::class, 'student_id');
  }

  public function submissionsWithPointsSum()
  {
    return $this->submissions()
      ->selectRaw('student_id, COALESCE(SUM(points),0) as points_sum')
      ->groupBy('student_id');
  }

  public function refreshTokens(): HasMany
  {
    return $this->hasMany(RefreshToken::class);
  }
}
