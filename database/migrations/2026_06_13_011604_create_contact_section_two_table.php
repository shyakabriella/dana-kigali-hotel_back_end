<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_section_two', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— SEND A MESSAGE');
            $table->string('subtitle')->default('Write to us.');
            $table->text('description')->default('Fill in the form below and we will get back to you within 24 hours. For urgent matters, please call us directly.');
            $table->string('right_image')->nullable();
            $table->string('image_caption')->default('DANA KIGALI HOTEL terrace view');
            $table->string('image_address')->default('KG 7 Ave, Kigali, Rwanda');
            $table->string('opening_hours_title')->default('— OPENING HOURS');
            $table->string('opening_hours_subtitle')->default('At your service.');
            $table->json('opening_hours')->nullable(); // Store opening hours as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_section_two');
    }
};