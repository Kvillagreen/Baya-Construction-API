<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('customer_code')->nullable()->unique()->after('id');
            $table->string('customer_type')->nullable()->after('customer_code');
            $table->string('email')->nullable()->after('contact_person');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('industry')->nullable()->after('location');
            $table->string('source')->nullable()->after('industry');
            $table->text('notes')->nullable()->after('status');
            $table->foreignUlid('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->foreignUlid('assigned_to')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('assigned_to');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->string('project_code')->nullable()->unique()->after('id');
            $table->foreignUlid('sale_id')->nullable()->after('customer_id')->constrained('opportunities')->nullOnDelete();
            $table->foreignUlid('quotation_id')->nullable()->after('sale_id')->constrained('quotations')->nullOnDelete();
            $table->string('title')->nullable()->after('quotation_id');
            $table->text('scope_of_work')->nullable()->after('description');
            $table->decimal('estimated_price', 15, 2)->default(0)->after('scope_of_work');
            $table->decimal('approved_price', 15, 2)->default(0)->after('estimated_price');
            $table->date('start_date')->nullable()->after('status');
            $table->date('target_completion_date')->nullable()->after('start_date');
            $table->timestamp('completed_at')->nullable()->after('target_completion_date');
            $table->foreignUlid('created_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            $table->foreignUlid('assigned_to')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->string('quotation_code')->nullable()->unique()->after('id');
            $table->foreignUlid('customer_id')->nullable()->after('quotation_code')->constrained('customers')->nullOnDelete();
            $table->foreignUlid('sale_id')->nullable()->after('project_id')->constrained('opportunities')->nullOnDelete();
            $table->string('title')->nullable()->after('sale_id');
            $table->text('description')->nullable()->after('title');
            $table->text('scope_of_work')->nullable()->after('description');
            $table->decimal('subtotal', 15, 2)->default(0)->after('scope_of_work');
            $table->decimal('discount', 15, 2)->default(0)->after('subtotal');
            $table->decimal('tax', 15, 2)->default(0)->after('discount');
            $table->decimal('total_amount', 15, 2)->default(0)->after('tax');
            $table->date('valid_until')->nullable()->after('status');
            $table->foreignUlid('prepared_by')->nullable()->after('valid_until')->constrained('users')->nullOnDelete();
            $table->foreignUlid('approved_by')->nullable()->after('prepared_by')->constrained('users')->nullOnDelete();
        });

        Schema::table('opportunities', function (Blueprint $table): void {
            $table->foreignUlid('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
            $table->foreignUlid('project_id')->nullable()->after('customer_id')->constrained('projects')->nullOnDelete();
            $table->foreignUlid('quotation_id')->nullable()->after('project_id')->constrained('quotations')->nullOnDelete();
            $table->string('sales_code')->nullable()->unique()->after('quotation_id');
            $table->string('title')->nullable()->after('sales_code');
            $table->text('description')->nullable()->after('title');
            $table->string('status')->default('Open')->after('description');
            $table->unsignedTinyInteger('probability')->default(0)->after('status');
            $table->date('expected_close_date')->nullable()->after('probability');
            $table->timestamp('closed_at')->nullable()->after('expected_close_date');
            $table->string('lost_reason')->nullable()->after('closed_at');
            $table->foreignUlid('assigned_to')->nullable()->after('lost_reason')->constrained('users')->nullOnDelete();
            $table->foreignUlid('created_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->string('sku')->nullable()->unique()->after('id');
            $table->text('description')->nullable()->after('item_name');
            $table->string('unit')->nullable()->after('category');
            $table->decimal('quantity_on_hand', 12, 2)->default(0)->after('unit');
            $table->decimal('cost_price', 15, 2)->default(0)->after('reorder_point');
            $table->decimal('selling_price', 15, 2)->default(0)->after('cost_price');
            $table->string('status')->default('Active')->after('selling_price');
            $table->ulid('category_id')->nullable()->after('status');
            $table->ulid('location_id')->nullable()->after('category_id');
            $table->ulid('supplier_id')->nullable()->after('location_id');
        });

        Schema::table('assets', function (Blueprint $table): void {
            $table->string('asset_code')->nullable()->unique()->after('id');
            $table->string('brand')->nullable()->after('asset_name');
            $table->string('model')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model');
            $table->decimal('purchase_cost', 15, 2)->default(0)->after('asset_acquired_date');
            $table->decimal('current_value', 15, 2)->default(0)->after('purchase_cost');
            $table->string('asset_condition')->nullable()->after('current_value');
            $table->string('location')->nullable()->after('asset_condition');
            $table->foreignUlid('released_to')->nullable()->after('assigned_employee_id')->constrained('users')->nullOnDelete();
            $table->foreignUlid('borrowed_by')->nullable()->after('released_to')->constrained('users')->nullOnDelete();
            $table->foreignUlid('custodian')->nullable()->after('borrowed_by')->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable()->after('custodian');
        });

        Schema::create('customer_contacts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('customer_activities', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('activity_type')->index();
            $table->string('subject');
            $table->text('notes')->nullable();
            $table->timestamp('activity_date')->index();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('customer_communication_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('channel')->index();
            $table->string('direction')->index();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('logged_at')->index();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('project_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('log_type')->index();
            $table->text('description');
            $table->timestamp('logged_at')->index();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('sale_id')->constrained('opportunities')->cascadeOnDelete();
            $table->string('stage_from')->nullable();
            $table->string('stage_to');
            $table->text('remarks')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('po_number')->unique();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUlid('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignUlid('sale_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->foreignUlid('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('Pending')->index();
            $table->date('po_date')->nullable()->index();
            $table->date('received_date')->nullable()->index();
            $table->string('attachment_url')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUlid('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignUlid('sale_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->foreignUlid('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignUlid('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('status')->default('Draft')->index();
            $table->date('due_date')->nullable()->index();
            $table->date('issued_date')->nullable()->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUlid('sale_id')->nullable()->constrained('opportunities')->nullOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_method')->index();
            $table->string('reference_number')->nullable()->index();
            $table->date('payment_date')->index();
            $table->foreignUlid('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_categories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_locations', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_suppliers', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('transaction_type')->index();
            $table->decimal('quantity', 12, 2);
            $table->decimal('previous_quantity', 12, 2)->default(0);
            $table->decimal('new_quantity', 12, 2)->default(0);
            $table->string('reference_type')->nullable()->index();
            $table->string('reference_id')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->foreignUlid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transaction_date')->index();
            $table->timestamps();
        });

        Schema::create('asset_assignments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUlid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('assigned_date')->index();
            $table->date('expected_return_date')->nullable()->index();
            $table->date('returned_date')->nullable()->index();
            $table->string('status')->default('Assigned')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('action')->index();
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            $table->foreignUlid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('released_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('borrowed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->foreignUlid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('entity_type')->index();
            $table->string('entity_id')->index();
            $table->string('action')->index();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->foreignUlid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('asset_logs');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_suppliers');
        Schema::dropIfExists('inventory_locations');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('sales_logs');
        Schema::dropIfExists('project_logs');
        Schema::dropIfExists('customer_communication_logs');
        Schema::dropIfExists('customer_activities');
        Schema::dropIfExists('customer_contacts');
    }
};
