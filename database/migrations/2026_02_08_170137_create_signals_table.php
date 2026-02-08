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
            $table->foreignId('domain_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('source_id')->index()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('url');
            $table->string('fingerprint')->unique()->index();
            $table->decimal('relevance_score', 5, 2)->default(0)->index();
            $table->text('summary')->nullable();
            $table->text('implications')->nullable();
            $table->unsignedTinyInteger('action_required')->default(0);
            $table->timestamp('published_at')->nullable()->index();
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
