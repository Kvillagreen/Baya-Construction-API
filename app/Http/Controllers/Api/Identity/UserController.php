<?php

namespace App\Http\Controllers\Api\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\StoreUserRequest;
use App\Http\Requests\Identity\UpdateUserRequest;
use App\Models\Identity\EndpointPermission;
use App\Models\Identity\Role;
use App\Models\Identity\UserEndpointPermission;
use App\Models\User;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::query()
                ->with('roles.permissions.endpointPermissions', 'endpointOverrides.endpointPermission')
                ->orderBy('name')
                ->paginate(15)
                ->through(fn (User $user) => FrontendApiPayload::user($user)),
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $user = DB::transaction(function () use ($payload) {
            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => $payload['password'],
                'user_type' => $payload['user_type'],
                'is_active' => $payload['is_active'] ?? true,
            ]);

            $user->roles()->sync($payload['role_ids'] ?? $this->defaultRoleIds($payload['user_type']));
            $this->syncEndpointOverrides($user, $payload['endpoint_overrides'] ?? []);

            return $user;
        });

        return response()->json([
            'message' => 'User created successfully.',
            'data' => FrontendApiPayload::user(
                $user->load('roles.permissions.endpointPermissions', 'endpointOverrides.endpointPermission')
            ),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $payload = $request->validated();

        if (array_key_exists('password', $payload) && blank($payload['password'])) {
            unset($payload['password']);
        }

        DB::transaction(function () use ($payload, $user): void {
            $user->fill($payload)->save();

            if (array_key_exists('role_ids', $payload)) {
                $user->roles()->sync($payload['role_ids'] ?? []);
            } elseif (array_key_exists('user_type', $payload)) {
                $user->roles()->sync($this->defaultRoleIds($payload['user_type']));
            }

            if (array_key_exists('endpoint_overrides', $payload)) {
                $this->syncEndpointOverrides($user, $payload['endpoint_overrides'] ?? []);
            }
        });

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => FrontendApiPayload::user(
                $user->load('roles.permissions.endpointPermissions', 'endpointOverrides.endpointPermission')
            ),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    private function defaultRoleIds(string $userType): array
    {
        $roleCode = $userType === 'Admin' ? 'admin' : 'operations_user';

        return Role::query()
            ->where('code', $roleCode)
            ->pluck('id')
            ->all();
    }

    private function syncEndpointOverrides(User $user, array $overrides): void
    {
        $user->endpointOverrides()->delete();

        if ($overrides === []) {
            return;
        }

        $definitions = collect($overrides)
            ->map(function (array $override): array {
                return [
                    'key' => sprintf('%s|%s', $override['endpoint'], $override['method']),
                    'allowed' => (bool) $override['allowed'],
                ];
            })
            ->keyBy('key');

        $endpointPermissions = EndpointPermission::query()
            ->where(function ($query) use ($definitions): void {
                foreach ($definitions as $key => $definition) {
                    [$endpoint, $method] = explode('|', $key);
                    $query->orWhere(fn ($nested) => $nested->where('endpoint', $endpoint)->where('method', $method));
                }
            })
            ->get();

        $rows = $endpointPermissions->map(function (EndpointPermission $endpointPermission) use ($definitions, $user): array {
            $key = sprintf('%s|%s', $endpointPermission->endpoint, $endpointPermission->method);

            return [
                'id' => (string) Str::ulid(),
                'user_id' => $user->id,
                'endpoint_permission_id' => $endpointPermission->id,
                'allowed' => $definitions[$key]['allowed'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        if ($rows !== []) {
            UserEndpointPermission::query()->insert($rows);
        }
    }
}
