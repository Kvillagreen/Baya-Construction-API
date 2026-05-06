<?php

namespace App\Http\Middleware;

use App\Models\Audit\SecurityEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrapHiddenCaptcha
{
    public function handle(Request $request, Closure $next): Response
    {
        $honeypot = (string) $request->input('honeypot', $request->input('website', ''));
        $startedAt = (int) $request->input('form_started_at', 0);

        $isBotLike = filled($honeypot) || ($startedAt > 0 && now()->timestamp - $startedAt < 2);

        if ($isBotLike) {
            SecurityEvent::query()->create([
                'event_type' => 'auth.hidden_captcha_triggered',
                'severity' => 'warning',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'email' => $request->input('email'),
                    'request_id' => $request->header('X-Request-Id'),
                ],
            ]);

            return response()->json([
                'message' => 'Unable to validate this request.',
            ], 422);
        }

        return $next($request);
    }
}
