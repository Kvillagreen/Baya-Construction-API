<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            return;
        }

        if (! Schema::hasColumn('personal_access_tokens', 'tokenable_id')) {
            return;
        }

        DB::statement('ALTER TABLE `personal_access_tokens` DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` CHAR(26) NOT NULL');
        Schema::table('personal_access_tokens', function (Blueprint $table): void {
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            return;
        }

        DB::statement('ALTER TABLE `personal_access_tokens` DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` BIGINT UNSIGNED NOT NULL');
        Schema::table('personal_access_tokens', function (Blueprint $table): void {
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
