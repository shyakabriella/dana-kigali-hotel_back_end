<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_fours', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— HOTEL FACILITIES');
            $table->string('subtitle')->default('The finest amenities, considered for you.');
            $table->text('description')->default('Everything that defines a perfect stay — quietly available, never imposed.');
            $table->json('amenities')->nullable(); // Store amenities as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_fours');
    }
};