<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_section_three', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— OUR HERITAGE');
            $table->string('subtitle')->default('A legacy from the Nile to the hills.');
            $table->json('timeline')->nullable(); // Store timeline as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_three');
    }
};