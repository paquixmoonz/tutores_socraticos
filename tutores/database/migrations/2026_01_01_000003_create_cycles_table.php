<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');
            $table->string('name')->nullable();
            $table->timestamps();
            $table->unique(['career_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycles');
    }
};
