<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  private float $refreshTokenExpireDays;

  /**
   * Create a new AuthController instance.
   *
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register', 'restore']]);
    $this->refreshTokenExpireDays = floatval(env('REFRESH_TOKEN_EXPIRE_DAYS', 30));
  }

  public function register(Request $request): JsonResponse
  {
    $validatedData = $request->validate([
      'first_name' => 'required|max:55',
      'last_name' => 'required|max:55',
      'email' => 'email|required|unique:users',
      'password' => 'required|confirmed'
    ]);

    $validatedData['password'] = bcrypt($request->password);

    $user = User::create($validatedData);
    $user->role = 'student';
    $accessToken = auth()->login($user);

    $refreshToken = new RefreshToken([
      'refresh_token' => Str::random(),
      'expires_at' => now()->addDays($this->refreshTokenExpireDays),
      'user_id' => auth()->user()->id,
    ]);
    $refreshToken->save();

    return $this->respondWithToken($accessToken, $refreshToken->refresh_token);
  }

  /**
   * Get a JWT via given credentials.
   */
  public function login(): JsonResponse
  {
    $credentials = request(['email', 'password']);
    $remember = request('remember') != null;

    if (!$token = auth()->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $refreshToken = null;
    if ($remember) {
      $refreshToken = new RefreshToken([
        'refresh_token' => Str::random(),
        'expires_at' => now()->addDays($this->refreshTokenExpireDays),
        'user_id' => auth()->user()->id,
      ]);
      $refreshToken->save();
    }

    return $this->respondWithToken($token, $refreshToken?->refresh_token);
  }

  public function restore(): JsonResponse
  {
    $refreshToken = request()->cookie('refresh_token');

    if (!$refreshToken) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $dbRefreshToken = RefreshToken::where('refresh_token', $refreshToken)->first();

    if (!$dbRefreshToken) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    if ($dbRefreshToken->expires_at < now() || $dbRefreshToken->revoked_at != null) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $accessToken = auth()->login($dbRefreshToken->user);

    $dbRefreshToken->refresh_token = Str::random();
    $dbRefreshToken->expires_at = now()->addDays($this->refreshTokenExpireDays);
    $dbRefreshToken->save();

    return $this->respondWithToken($accessToken, $dbRefreshToken->refresh_token);
  }

  /**
   * Get the authenticated User.
   */
  public function me(): JsonResponse
  {
    return response()->json(auth()->user());
  }

  /**
   * Log the user out (Invalidate the token).
   */
  public function logout(): JsonResponse
  {
    $refreshToken = request()->cookie('refresh_token');

    if ($refreshToken) {
      $dbRefreshToken = RefreshToken::where('refresh_token', $refreshToken)->first();
      $dbRefreshToken->revoked_at = now();
      $dbRefreshToken->save();
    }

    auth()->logout();

    return response()
      ->json(['message' => 'Successfully logged out'])
      ->withoutCookie('refresh_token');
  }

  /**
   * Refresh a token.
   */
  public function refresh(): JsonResponse
  {
    return $this->respondWithToken(auth()->refresh());
  }

  /**
   * Get the token array structure.
   */
  protected function respondWithToken(string $token, string|null $refreshToken = null): JsonResponse
  {
    $response = response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ]);

    if ($refreshToken) {
      $cookie = cookie('refresh_token',
        $refreshToken,
        $this->refreshTokenExpireDays * 24 * 60,
        null,
        null,
        request()->secure()
      );
      $response->cookie($cookie);
    }

    return $response;
  }
}
