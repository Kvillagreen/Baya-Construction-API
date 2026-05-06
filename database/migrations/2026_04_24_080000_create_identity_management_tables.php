<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('module')->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('endpoint_permissions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('service')->index();
            $table->string('endpoint')->index();
            $table->string('method', 10)->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_roles', function (Blueprint $table): void {
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('role_permissions', function (Blueprint $table): void {
            $table->foreignUlid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignUlid('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('user_endpoint_permissions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('endpoint_permission_id')->constrained('endpoint_permissions')->cascadeOnDelete();
            $table->boolean('allowed')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'endpoint_permission_id']);
        });

        Schema::create('failed_login_attempts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('email')->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable()->index();
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->string('geo_hint', 10)->nullable();
            $table->timestamp('last_attempt_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_login_attempts');
        Schema::dropIfExists('user_endpoint_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('endpoint_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
