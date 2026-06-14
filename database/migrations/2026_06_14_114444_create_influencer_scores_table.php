<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('influencer_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('criterion_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('raw_value', 15, 2);
            $table->unsignedTinyInteger('likert_value');
            $table->timestamps();

            $table->unique(['influencer_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('influencer_scores');
    }
};
