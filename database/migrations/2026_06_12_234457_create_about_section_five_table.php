<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_section_five', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— COME STAY');
            $table->string('subtitle')->default('A warm welcome is waiting.');
            $table->text('description')->default('Reserve a room and experience the true meaning of home in the heart of Kigali.');
            $table->string('left_image')->nullable();
            $table->string('button_text')->default('Back to Home');
            $table->string('secondary_text')->default('Reserve a Stay');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_five');
    }
};