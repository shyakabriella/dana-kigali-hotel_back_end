<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences_section_two', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— BEGIN YOUR STORY');
            $table->string('subtitle')->default('Reserve your first experience.');
            $table->text('description')->default('Many experiences are exclusive to guests. Book a room and unlock the full ridge.');
            $table->string('button_one_text')->default('Reserve a Stay');
            $table->string('button_two_text')->default('View Rooms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences_section_two');
    }
};