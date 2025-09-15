<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('corrective_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_map_id')->constrained('student_academic_mappings')->onDelete('cascade');
            $table->text('measure');
            $table->text('reason');
            $table->timestamp('implemented_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('academic_map_id');
            $table->index('implemented_at');
            $table->index('resolved_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('corrective_measures');
    }
};