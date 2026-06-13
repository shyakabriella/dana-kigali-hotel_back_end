<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_fives', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— RELAXING MOMENTS');
            $table->string('subtitle')->default('Spa & Thermal Center.');
            $table->text('description')->default('A subterranean retreat of stone, candlelight and water — designed for stillness.');
            $table->string('left_image')->nullable();
            $table->json('items')->nullable(); // Store items as JSON array
            $table->string('button_text')->default('reserve treatment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_fives');
    }
};