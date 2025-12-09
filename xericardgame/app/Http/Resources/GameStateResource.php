<?php

namespace App\Http\Resources;

class GameStateResource extends GameBaseResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'opponent_cards' => $this->opponent_cards,
            'current_player_id' => $this->current_player_id,
            'game_status' => $this->status,
        ]);
    }
}
