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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('source'); // Keep as descriptive source name if needed, or remove? I'll keep it for now but the FK is primary.
            $table->string('url');
            $table->string('fingerprint')->unique();
            $table->decimal('relevance_score', 5, 2)->default(0);
            $table->text('summary')->nullable();
            $table->text('implications')->nullable();
            $table->boolean('action_required')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
