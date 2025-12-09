<?php

namespace App\Http\Resources;

class GameBaseResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'game_id' => $this->game_id,
            'player_number' => $this->player_number,
            'your_hand' => $this->your_hand,
            'table_cards' => $this->table_cards,
        ];
    }
}
