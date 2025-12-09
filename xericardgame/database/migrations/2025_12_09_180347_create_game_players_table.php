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
    Schema::create('game_players', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->unsignedTinyInteger('player_number'); // 1 or 2
        $table->integer('score')->default(0);
        $table->integer('xeri_count')->default(0);
        $table->timestamps();

        $table->unique(['game_id', 'player_number']);
        $table->unique(['game_id', 'user_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('game_players');
}

};
