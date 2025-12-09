<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;
use App\Models\User;    

class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'card_played',
        'captured_cards',
        'xeri',
        'move_number',
    ];

    protected $casts = [
        'captured_cards' => 'json',
        'xeri' => 'boolean',
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
