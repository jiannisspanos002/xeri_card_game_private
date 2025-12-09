<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Hand;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
     public function games()
    {
        return $this->hasMany(Game::class, 'creator_id');
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function hands()
    {
        return $this->hasMany(Hand::class);
    }
}
