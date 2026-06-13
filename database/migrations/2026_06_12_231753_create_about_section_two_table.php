<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_section_two', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— OUR VALUES');
            $table->string('subtitle')->default('The spirit of Dana, in everything we do.');
            $table->json('values')->nullable(); // Store values as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_two');
    }
};