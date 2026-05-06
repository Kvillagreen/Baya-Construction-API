<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('position');
            $table->date('date_hired')->index();
            $table->string('department')->index();
            $table->string('employment_status')->index();
            $table->text('base_salary_encrypted');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_leave_balances', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('leave_type')->index();
            $table->decimal('balance_days', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
        Schema::dropIfExists('employees');
    }
};
