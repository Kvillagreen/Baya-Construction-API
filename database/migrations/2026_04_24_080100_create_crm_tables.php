<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('company_name')->index();
            $table->string('company_address');
            $table->string('region')->index();
            $table->string('location')->index();
            $table->string('status')->index();
            $table->string('contact_person');
            $table->string('contact_number', 30);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('project_name')->index();
            $table->text('description')->nullable();
            $table->string('status')->index();
            $table->string('invoice_number')->nullable()->index();
            $table->string('purchase_order_number')->nullable()->index();
            $table->text('project_costing_amount_encrypted');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotations', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('content');
            $table->string('status')->index();
            $table->text('quotation_price_encrypted');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_timelines', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('label');
            $table->text('details')->nullable();
            $table->date('timeline_date')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_timelines');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('customers');
    }
};
