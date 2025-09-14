<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('academic_year_id')->constrained('academicyear_settings')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('title');
            $table->index('academic_year_id');
            $table->index('is_active');
            $table->index(['academic_year_id', 'is_active']);
            
            // Unique constraint to prevent duplicate exam titles for same academic year
            $table->unique(['title', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_settings');
    }
};