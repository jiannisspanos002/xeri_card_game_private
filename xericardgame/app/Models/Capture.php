<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Game;
use App\Models\User;

class Capture extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'cards',
    ];

    protected $casts = [
        'cards' => 'json',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
