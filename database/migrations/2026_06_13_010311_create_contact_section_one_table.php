<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_section_one', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— REACH OUT');
            $table->string('subtitle')->default('We are here for you.');
            $table->text('description')->default('Whether you have a question, a special request, or simply want to say hello — our team is ready to welcome you with the warmth of the DANA family.');
            $table->json('cards')->nullable(); // Store cards as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_section_one');
    }
};