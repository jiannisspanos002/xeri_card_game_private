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
    Schema::create('games', function (Blueprint $table) {
        $table->id();
        $table->foreignId('creator_id')->constrained('users');
        $table->enum('status', ['waiting', 'active', 'finished'])->default('waiting');
        $table->foreignId('current_player_id')->nullable()->constrained('users');

        // JSON fields (Postgres: jsonb)
        $table->jsonb('deck')->nullable();
        $table->jsonb('table_cards')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('games');
}

};
