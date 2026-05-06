<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_time_records', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('cutoff')->index();
            $table->date('period_from');
            $table->date('period_to');
            $table->string('day_type');
            $table->string('attendance');
            $table->string('leave_type')->nullable();
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });

        Schema::create('leave_entries', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('leave_type')->index();
            $table->date('date_from')->index();
            $table->date('date_to')->index();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_entries');
        Schema::dropIfExists('daily_time_records');
    }
};
