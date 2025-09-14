<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_academic_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academicyear_settings');
            $table->foreignId('grade_id')->constrained('grade_settings');
            $table->foreignId('stream_id')->nullable()->constrained('stream_settings');
            $table->foreignId('shift_id')->nullable()->constrained('shift_settings');
            $table->foreignId('section_id')->nullable()->constrained('section_settings');
            $table->boolean('is_active_year')->default(false);
            $table->timestamps();
            
            // Prevent duplicate mappings for same student and academic year
            $table->unique(['student_id', 'academic_year_id']);
            
            // Indexes
            $table->index('is_active_year');
            $table->index(['student_id', 'is_active_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_mappings');
    }
};