<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('active_status');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_settings');
    }
};