<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_academic_attendance_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_map_id')->constrained('student_academic_mappings')->onDelete('cascade');
            $table->foreignId('attendance_month_id')->constrained('attendance_month_settings')->onDelete('cascade');
            $table->integer('present_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('school_days')->default(0);
            $table->timestamps();
            
            // Prevent duplicate attendance records
            $table->unique(
                ['student_id', 'academic_map_id', 'attendance_month_id'],
                'stu_attendance_unique'
            );            
            
            // Indexes for performance - provide custom shorter names
            $table->index('student_id', 'saam_student_id_idx');
            $table->index('academic_map_id', 'saam_academic_map_id_idx');
            $table->index('attendance_month_id', 'saam_att_month_id_idx');
            
            // Composite index with custom name
            $table->index(
                ['student_id', 'attendance_month_id'],
                'saam_student_att_month_idx'
            );
            
            $table->index('present_days', 'saam_present_days_idx');
            $table->index('absent_days', 'saam_absent_days_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_attendance_mappings');
    }
};