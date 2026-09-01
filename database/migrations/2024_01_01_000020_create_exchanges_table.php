<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('counterpart_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('value_declaration_id')->nullable()->constrained('value_declarations')->onDelete('set null');
            $table->string('title');
            $table->text('contract_terms');
            $table->string('status')->default('pending');
            $table->text('dispute_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};