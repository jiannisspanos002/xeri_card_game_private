<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GamePlayer;
use App\Models\User;
use App\Models\Move;
use App\Models\Hand;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'status',
        'current_player_id',
        'deck',
        'table_cards',
    ];

    protected $casts = [
        'deck' => 'json',
        'table_cards' => 'json',
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function currentPlayer()
    {
        return $this->belongsTo(User::class, 'current_player_id');
    }

    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function hands()
    {
        return $this->hasMany(Hand::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}

