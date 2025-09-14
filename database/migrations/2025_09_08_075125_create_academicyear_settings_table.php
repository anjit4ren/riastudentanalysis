<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academicyear_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('running')->default(false);
            $table->date('starting_date');
            $table->date('ending_date');
            $table->timestamps();
            
            // Optional: Add index for better performance
            $table->index('running');
            $table->index('starting_date');
            $table->softDeletes(); // This adds the deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academicyear_settings');
    }
};