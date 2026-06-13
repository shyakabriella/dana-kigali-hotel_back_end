<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— SIGNATURE EXPERIENCES');
            $table->string('subtitle')->default('Experiences');
            $table->string('destination')->default('Home/Experiences');
            $table->string('background_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences_hero');
    }
};