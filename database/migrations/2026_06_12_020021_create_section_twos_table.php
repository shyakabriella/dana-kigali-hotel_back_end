<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_twos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— THE RIDGE COLLECTION');
            $table->string('subtitle')->default('Rooms & Suites');
            $table->json('rooms')->nullable(); // Store rooms as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_twos');
    }
};