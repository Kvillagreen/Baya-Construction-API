<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->string('salary_type')->default('Monthly')->after('employment_status');
            $table->decimal('daily_rate', 12, 2)->nullable()->after('base_salary_encrypted');
            $table->decimal('sss_contribution', 12, 2)->default(0)->after('daily_rate');
            $table->decimal('philhealth_contribution', 12, 2)->default(0)->after('sss_contribution');
            $table->decimal('pagibig_contribution', 12, 2)->default(0)->after('philhealth_contribution');
            $table->decimal('withholding_tax', 12, 2)->default(0)->after('pagibig_contribution');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->dropColumn([
                'salary_type',
                'daily_rate',
                'sss_contribution',
                'philhealth_contribution',
                'pagibig_contribution',
                'withholding_tax',
            ]);
        });
    }
};
