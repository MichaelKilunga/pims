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
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('setup_completed_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('member')->after('email');
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['owner_id', 'setup_completed_at']);
        });
    }
};
