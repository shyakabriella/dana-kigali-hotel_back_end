<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_ones', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description');
            $table->string('left_image')->nullable();
            $table->string('card1_title');
            $table->text('card1_description');
            $table->string('card2_title');
            $table->text('card2_description');
            $table->string('bottom_card_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_ones');
    }
};