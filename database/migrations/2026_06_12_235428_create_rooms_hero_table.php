<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— THE RIDGE COLLECTION');
            $table->string('subtitle')->default('Rooms & Suites');
            $table->string('destination')->default('Home/Rooms');
            $table->string('background_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms_hero');
    }
};