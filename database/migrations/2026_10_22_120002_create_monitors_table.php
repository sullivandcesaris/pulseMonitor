<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('target_url');
            $table->string('method', 10)->default('GET');
            $table->unsignedInteger('interval_seconds')->default(60);
            $table->unsignedSmallInteger('expected_status')->default(200);
            $table->unsignedSmallInteger('timeout_seconds')->default(10);
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('next_check_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'is_enabled']);
            $table->index(['is_enabled', 'next_check_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
