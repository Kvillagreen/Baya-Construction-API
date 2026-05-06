<?php

namespace App\Http\Middleware;

use App\Models\Identity\EndpointPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEndpointPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $method = $request->method();
        $endpoint = $this->normalizeEndpoint($request->route()?->uri() ?? $request->path());

        $override = $user->endpointOverrides()
            ->whereHas('endpointPermission', function ($query) use ($endpoint, $method): void {
                $query->where('endpoint', $endpoint)->where('method', $method);
            })
            ->first();

        if ($override) {
            if (! $override->allowed) {
                return response()->json(['message' => 'This endpoint is blocked for this user.'], 403);
            }

            return $next($request);
        }

        $hasRolePermission = EndpointPermission::query()
            ->where('endpoint', $endpoint)
            ->where('method', $method)
            ->whereHas('permission.roles.users', fn ($query) => $query->where('users.id', $user->getAuthIdentifier()))
            ->exists();

        if (! $hasRolePermission) {
            return response()->json(['message' => 'You do not have access to this endpoint.'], 403);
        }

        return $next($request);
    }

    private function normalizeEndpoint(string $endpoint): string
    {
        return ltrim((string) str($endpoint)->after('api/v1/'), '/');
    }
}
