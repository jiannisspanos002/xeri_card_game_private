<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('hands', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Card list per player
        $table->jsonb('cards')->nullable();

        $table->timestamps();

        $table->unique(['game_id', 'user_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('hands');
}

};
