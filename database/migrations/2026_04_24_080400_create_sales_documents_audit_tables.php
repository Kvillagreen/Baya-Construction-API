<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('account_name')->index();
            $table->string('project')->index();
            $table->string('stage')->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('owner_name');
            $table->date('close_date')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('documents', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->nullableMorphs('documentable');
            $table->string('document_type')->index();
            $table->string('disk', 64);
            $table->string('storage_path');
            $table->string('original_name');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->foreignUlid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('security_events', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('event_type')->index();
            $table->string('severity')->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('opportunities');
    }
};
