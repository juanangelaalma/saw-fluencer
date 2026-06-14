<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->string('label');
            $table->decimal('min_value', 15, 2)->nullable();
            $table->decimal('max_value', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['criterion_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_criteria');
    }
};
