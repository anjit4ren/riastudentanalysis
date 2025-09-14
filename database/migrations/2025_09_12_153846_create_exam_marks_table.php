<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_map_id')->constrained('student_academic_mappings')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exam_settings')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('grade_stream_subjects')->onDelete('cascade');
            $table->string('marks_obtained', 20)->nullable();
            $table->string('grade', 10)->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Prevent duplicate entries for same student+exam+subject
            $table->unique(['student_id', 'academic_map_id', 'exam_id', 'subject_id']);
            
            // Indexes for performance
            $table->index('student_id');
            $table->index('academic_map_id');
            $table->index('exam_id');
            $table->index('subject_id');
            $table->index(['student_id', 'exam_id']);
            $table->index(['academic_map_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};