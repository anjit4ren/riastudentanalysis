<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_month_settings', function (Blueprint $table) {
            $table->id();
            $table->string('month_name');
            $table->integer('order')->default(0); // Added order field for arrangement
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('month_name');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_month_settings');
    }
};