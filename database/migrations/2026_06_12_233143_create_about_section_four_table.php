<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_section_four', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— OUR FAMILY');
            $table->string('subtitle')->default('A team that welcomes you home.');
            $table->json('team_members')->nullable(); // Store team members as JSON array with images
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_four');
    }
};