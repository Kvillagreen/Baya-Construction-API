<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('salary_type')->default('Monthly')->index();
            $table->decimal('monthly_rate', 12, 2)->default(0);
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->decimal('hourly_rate', 12, 2)->default(0);
            $table->decimal('overtime_rate', 12, 2)->default(0);
            $table->date('effective_from')->index();
            $table->date('effective_to')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('employee_contributions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('label');
            $table->decimal('employee_share', 12, 2)->default(0);
            $table->decimal('employer_share', 12, 2)->default(0);
            $table->date('effective_from')->nullable()->index();
            $table->date('effective_to')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('employee_deductions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('label');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('frequency')->default('One Time')->index();
            $table->unsignedInteger('remaining_periods')->default(1);
            $table->date('effective_from')->nullable()->index();
            $table->date('effective_to')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('payroll_periods', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('cutoff')->index();
            $table->date('date_from')->index();
            $table->date('date_to')->index();
            $table->string('status')->default('Draft')->index();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->unique(['cutoff', 'date_from', 'date_to']);
        });

        Schema::create('payroll_runs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('status')->default('Draft')->index();
            $table->json('salary_snapshot');
            $table->json('attendance_snapshot');
            $table->json('leave_snapshot');
            $table->json('overtime_snapshot');
            $table->decimal('payable_days', 12, 2)->default(0);
            $table->decimal('days_present', 12, 2)->default(0);
            $table->decimal('paid_leave_days', 12, 2)->default(0);
            $table->decimal('unpaid_leave_days', 12, 2)->default(0);
            $table->decimal('regular_hours', 12, 2)->default(0);
            $table->decimal('overtime_hours', 12, 2)->default(0);
            $table->decimal('regular_pay', 12, 2)->default(0);
            $table->decimal('paid_leave_pay', 12, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('total_contributions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->timestamp('finalized_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['payroll_period_id', 'employee_id']);
        });

        Schema::create('payroll_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->string('category')->index();
            $table->string('code')->index();
            $table->string('label');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('payslips', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('payslip_number')->unique();
            $table->json('payload');
            $table->timestamp('generated_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('employee_contributions');
        Schema::dropIfExists('employee_salaries');
    }
};
