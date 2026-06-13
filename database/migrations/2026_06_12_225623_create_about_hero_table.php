<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— OUR STORY');
            $table->string('subtitle')->default('About DANA KIGALI HOTEL');
            $table->string('destination')->default('Home/About');
            $table->string('background_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_hero');
    }
};