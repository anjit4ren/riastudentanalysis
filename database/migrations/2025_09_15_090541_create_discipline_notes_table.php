<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discipline_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_map_id')->constrained('student_academic_mappings')->onDelete('cascade');
            $table->text('note');
            $table->text('interactor');
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('academic_map_id');
            $table->index('interactor');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discipline_notes');
    }
};