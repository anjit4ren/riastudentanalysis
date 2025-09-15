<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_map_id')->constrained('student_academic_mappings')->onDelete('cascade');
            $table->string('remark_role', 50);
            $table->string('remark_person', 255);
            $table->text('remark_note');
            $table->date('date');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('academic_map_id');
            $table->index('remark_role');
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('remarks');
    }
};