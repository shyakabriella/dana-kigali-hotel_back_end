<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_eights', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('— MEETINGS & EVENTS');
            $table->text('description')->default('A warm, exquisite, and elevated space for occasions of every scale.');
            $table->string('button_text')->default('Plan Your Event');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_eights');
    }
};