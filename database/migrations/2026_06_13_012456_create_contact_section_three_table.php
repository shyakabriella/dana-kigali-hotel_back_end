<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_section_three', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— INSTANT MESSAGING');
            $table->string('subtitle')->default('Prefer to chat on WhatsApp?');
            $table->text('description')->default('Send us a message anytime on WhatsApp and our team will respond as soon as possible. Perfect for quick questions and last-minute requests.');
            $table->string('button_one_text')->default('Chat on WhatsApp');
            $table->string('button_two_text')->default('View Rooms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_section_three');
    }
};