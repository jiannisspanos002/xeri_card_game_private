<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'player_number',
        'score',
        'xeri_count',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hand()
    {
        return $this->hasOne(Hand::class, 'user_id', 'user_id')
                    ->where('game_id', $this->game_id);
    }
}

