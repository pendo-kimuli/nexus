<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('value_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->text('skills_offered');
            $table->text('skills_sought');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('value_declarations');
    }
};