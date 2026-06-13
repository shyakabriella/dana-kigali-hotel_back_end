<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_section_one', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— WELCOME');
            $table->string('subtitle')->default('Your Home Away from Home.');
            $table->longText('description');
            $table->string('right_image')->nullable();
            $table->string('card_title')->default('Please feel at home.');
            $table->json('stats')->nullable(); // Store stats as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_one');
    }
};