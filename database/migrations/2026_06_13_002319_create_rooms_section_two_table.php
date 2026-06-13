<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms_section_two', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— NEED HELP CHOOSING?');
            $table->string('subtitle')->default('Our concierge is one call away.');
            $table->string('button_text')->default('Speak To Concierge');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms_section_two');
    }
};