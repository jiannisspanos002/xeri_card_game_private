<?php

namespace App\Helpers;

use App\Models\Game;
use App\Models\Capture;
use App\Models\GamePlayer;

class EndGameHelper
{
    public static function finalizeGame(Game $game): void
    {
        $game_id = $game->id;

        if (!empty($game->table_cards) && $game->last_captor_user_id) {

            $capture = Capture::firstOrCreate(
                ['game_id' => $game_id, 'user_id' => $game->last_captor_user_id],
                ['cards' => []]
            );

            $capture->update([
                'cards' => array_merge($capture->cards ?? [], $game->table_cards)
            ]);
        }

        $game->update(['table_cards' => []]);


        $players = GamePlayer::where('game_id', $game_id)->get();

        $captures = Capture::where('game_id', $game_id)->get()->keyBy('user_id');

        $card_counts = [];
        $diamond_counts = [];

        foreach ($players as $player) {
            $cards = $captures[$player->user_id]->cards ?? [];

            $card_counts[$player->user_id] = count($cards);

            $diamond_counts[$player->user_id] = count(array_filter($cards, function ($card) {
                return str_ends_with($card, 'D');
            }));
        }

        $most_cards_user = array_keys($card_counts, max($card_counts))[0];
        $most_diamonds_user = array_keys($diamond_counts, max($diamond_counts))[0];

        foreach ($players as $player) {
            $user_id = $player->user_id;

            $score = 0;

            if ($user_id == $most_cards_user) {
                $score += 3;
            }

            if ($user_id == $most_diamonds_user) {
                $score += 1;
            }

            $score += ($player->xeri_count * 10);

            $player->update(['score' => $score]);
        }

        $game->update(['status' => 'completed']);
    }
}
