<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_sevens', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— GUEST WORDS');
            $table->string('subtitle')->default('Quiet praise, gratefully received.');
            $table->json('testimonials')->nullable(); // Store testimonials as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_sevens');
    }
};