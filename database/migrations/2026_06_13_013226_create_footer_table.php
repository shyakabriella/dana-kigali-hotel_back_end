<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name')->default('DANA KIGALI HOTEL');
            $table->text('description')->default('A welcoming home in Kigali, Rwanda, where kindness, family, and hospitality come first.');
            $table->string('address')->default('KG 7 Ave, Kigali, Rwanda');
            $table->string('phone')->default('+250 788 000 000');
            $table->string('email')->default('stay@danakigali.rw');
            $table->string('newsletter_placeholder')->default('Your email');
            $table->string('newsletter_button')->default('Join');
            $table->json('social_links')->nullable(); // Store social media links as JSON
            $table->string('copyright_text')->default('© DANA KIGALI HOTEL. All rights reserved.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer');
    }
};