<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('eid')->unique();
            $table->string('name');
            $table->string('roll_no')->nullable();
            $table->text('address')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('see_gpa')->nullable();
            $table->string('parents_name');
            $table->string('parents_contact');
            $table->string('photo')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('eid');
            $table->index('status');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};