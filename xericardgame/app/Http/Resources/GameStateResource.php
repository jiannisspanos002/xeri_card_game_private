<?php

namespace App\Http\Resources;

use App\Models\GamePlayer;

class GameStateResource extends GameBaseResource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);

        if ($this['status'] === 'completed') {

            $players = GamePlayer::where('game_id', $this['game_id'])->get();

            $results = [];

            foreach ($players as $player) {
                $results[] = [
                    'user_id' => $player->user_id,
                    'player_number' => $player->player_number,
                    'score' => $player->score,
                    'xeri_count' => $player->xeri_count,
                ];
            }

            $winner = collect($results)->sortByDesc('score')->first();

            $data['results'] = $results;
            $data['winner'] = $winner;

            return $data;
        }

        $data['opponent_cards'] = $this['opponent_cards'];
        $data['current_player_id'] = $this['current_player_id'];
        $data['game_status'] = $this['status'];

        return $data;
    }
}
