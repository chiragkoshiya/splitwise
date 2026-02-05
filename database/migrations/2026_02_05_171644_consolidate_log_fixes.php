<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->string('entity_type')->nullable()->after('action');
            }
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('financial_logs', function (Blueprint $table) {
            if (Schema::hasColumn('financial_logs', 'effect')) {
                $table->renameColumn('effect', 'type');
            } elseif (!Schema::hasColumn('financial_logs', 'type')) {
                $table->string('type')->after('amount');
            }
        });

        Schema::table('auth_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('auth_logs', 'success')) {
                $table->boolean('success')->default(true)->after('user_agent');
            }
            if (!Schema::hasColumn('auth_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('success');
            }
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse operations as possible
    }
};
