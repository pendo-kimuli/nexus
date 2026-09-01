<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('timeliness_score', 5, 2)->default(0);
            $table->decimal('rating_score', 5, 2)->default(0);
            $table->decimal('completeness_score', 5, 2)->default(0);
            $table->decimal('dispute_score', 5, 2)->default(100);
            $table->boolean('capital_eligible')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_scores');
    }
};