<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences_section_one', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— SIX WAYS TO REMEMBER');
            $table->string('subtitle')->default('Days that linger.');
            $table->text('description')->default('At DANA KIGALI HOTEL, the landscape is not a backdrop — it is the main event. Each experience is designed to draw you deeper into the ridge, the forest, and the quiet rhythm of mountain life.');
            $table->json('experiences')->nullable(); // Store experiences as JSON array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences_section_one');
    }
};