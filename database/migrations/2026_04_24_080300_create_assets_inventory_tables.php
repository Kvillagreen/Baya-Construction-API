<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assets', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('asset_name')->index();
            $table->unsignedInteger('asset_stocks')->default(0);
            $table->string('model_serial_number')->index();
            $table->date('asset_acquired_date')->nullable()->index();
            $table->text('asset_price_encrypted');
            $table->foreignUlid('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('status')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('item_name')->index();
            $table->string('category')->index();
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('reserved_stock')->default(0);
            $table->unsignedInteger('reorder_point')->default(0);
            $table->string('warehouse')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
