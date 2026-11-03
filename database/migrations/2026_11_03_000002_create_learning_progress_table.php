<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('learner_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('completed_lessons')->nullable();
            $table->json('completed_activities')->nullable();
            $table->string('current_lesson', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_progress');
    }
};
