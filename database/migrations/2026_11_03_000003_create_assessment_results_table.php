<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->string('quiz_type', 20);
            $table->unsignedTinyInteger('score');
            $table->unsignedTinyInteger('total');
            $table->json('answers');
            $table->json('recommendations')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
            $table->index(['learner_id', 'quiz_type', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
