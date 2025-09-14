<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_stream_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->foreignId('grade_id')->constrained('grade_settings')->onDelete('cascade');
            $table->foreignId('stream_id')->nullable()->constrained('stream_settings')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('subject_name');
            $table->index('grade_id');
            $table->index('stream_id');
            $table->index('order');
            $table->index('is_active');
            $table->index(['grade_id', 'stream_id']);
            
            // Unique constraint to prevent duplicate subjects for same grade+stream
            $table->unique(['subject_name', 'grade_id', 'stream_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_stream_subjects');
    }
};