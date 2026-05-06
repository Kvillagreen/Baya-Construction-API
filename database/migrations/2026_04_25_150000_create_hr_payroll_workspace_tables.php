<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date')->index();
            $table->decimal('hours', 8, 2);
            $table->boolean('approved')->default(false)->index();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_adjustments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('type')->index();
            $table->timestamps();
        });

        Schema::create('payroll_cutoff_refreshes', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('cutoff')->index();
            $table->date('period_from');
            $table->date('period_to');
            $table->timestamp('refreshed_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_cutoff_refreshes');
        Schema::dropIfExists('payroll_adjustments');
        Schema::dropIfExists('overtime_logs');
    }
};
