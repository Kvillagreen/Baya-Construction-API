<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Audit\SecurityEvent;
use App\Models\Identity\FailedLoginAttempt;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = \App\Models\User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            $attempt = FailedLoginAttempt::query()->firstOrNew([
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
            ]);

            $attempt->fill([
                'user_agent' => $request->userAgent(),
                'device_fingerprint' => (string) $request->header('X-Device-Fingerprint', 'unknown'),
                'geo_hint' => (string) $request->header('CF-IPCountry', 'unknown'),
                'attempt_count' => ($attempt->attempt_count ?? 0) + 1,
                'last_attempt_at' => now(),
            ])->save();

            if ($attempt->attempt_count >= 5) {
                SecurityEvent::query()->create([
                    'event_type' => 'auth.failed_threshold_reached',
                    'severity' => 'critical',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'metadata' => [
                        'email' => $credentials['email'],
                        'attempt_count' => $attempt->attempt_count,
                    ],
                ]);
            }

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect or the account is inactive.'],
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'message' => 'Authenticated successfully.',
            'data' => [
                'token' => $token,
                'user' => FrontendApiPayload::user(
                    $user->load('roles.permissions.endpointPermissions', 'endpointOverrides.endpointPermission')
                ),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
                ? FrontendApiPayload::user(
                    $request->user()->load('roles.permissions.endpointPermissions', 'endpointOverrides.endpointPermission')
                )
                : null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
