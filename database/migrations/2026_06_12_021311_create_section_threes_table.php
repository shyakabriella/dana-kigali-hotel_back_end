<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_threes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— SIGNATURE EXPERIENCES');
            $table->string('subtitle')->default('Days that linger in memory.');
            $table->json('cards')->nullable(); // Store cards as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_threes');
    }
};