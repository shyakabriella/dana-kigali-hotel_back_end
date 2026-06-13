<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms_section_one', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— SIX WAYS TO STAY');
            $table->string('subtitle')->default('Choose your ridge.');
            $table->text('description')->default('Each room at DANA KIGALI HOTEL is shaped around its view — from compact alpine retreats to suites with private terraces and stone fireplaces. All include daily housekeeping, hand-finished linens, and unhurried mornings.');
            $table->json('rooms')->nullable(); // Store rooms as JSON array with multiple images
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms_section_one');
    }
};