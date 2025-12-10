<?php

namespace App\Helpers;

use App\Models\Capture;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Hand;
use App\Helpers\EndGameHelper;

class CardHelper
{
    /**
     * Extract the numeric/letter value from a card string.
     * Example: "7D" → "7", "10H" → "10", "QS" → "Q"
     */
    public static function value(string $card): string
    {
        return preg_replace('/[HDSC]/', '', $card);
    }

    /**
     * Test whether a played card captures the table.
     *
     * According to Ξερή rules:
     * - You can capture if the played card's value matches 
     *   the value of the top table card.
     * 
     * Returns:
     *   [bool $captures, bool $xeri]
     */
    public static function testCapture(string $played_card, array $table_cards): array
    {
        if (empty($table_cards)) {
            return [false, false];
        }

        $played_value = self::value($played_card);
        $top_card = end($table_cards);
        $top_value = self::value($top_card);

        $captures = $played_value === $top_value;
        $xeri = $captures && count($table_cards) === 1;

        return [$captures, $xeri];
    }

    public static function applyCapture(
        string $played_card,
        array  $table_cards,
        int    $game_id,
        int    $user_id
    ): array {
        [$captures, $xeri] = self::testCapture($played_card, $table_cards);

        if (!$captures) {
            return [
                'table' => array_merge($table_cards, [$played_card]),
                'captures' => false,
                'xeri' => false
            ];
        }

        $capture_model = Capture::firstOrCreate(
            ['game_id' => $game_id, 'user_id' => $user_id],
            ['cards' => []]
        );

        $current_captured = $captureModel->cards ?? [];
        $new_captured = array_merge($current_captured, $table_cards, [$played_card]);

        $capture_model->update(['cards' => $new_captured]);

        $game = Game::find($game_id);
        $game->update(['last_captor_user_id' => $user_id]);


        if ($xeri) {
            $player = GamePlayer::where('game_id', $game_id)
                ->where('user_id', $user_id)
                ->first();

            if ($player) {
                $player->increment('xeri_count');
            }
        }

        return [
            'table' => [],
            'captures' => true,
            'xeri' => $xeri
        ];
    }

    public static function handleEmptyHands(Game $game): void
    {
        $game_id = $game->id;

        $hands = Hand::where('game_id', $game_id)
            ->whereIn('user_id', $game->players()->pluck('user_id'))
            ->get();

        $all_empty = $hands->every(fn($hand) => empty($hand->cards));

        if (!$all_empty) {
            return;
        }

        if (empty($game->deck)) {
            EndGameHelper::finalizeGame($game);
            return;
        }

        $deck = $game->deck;

        foreach ($game->players as $p) {
            $new_hand = array_slice($deck, 0, 6);
            $deck = array_slice($deck, 6);

            Hand::where('game_id', $game_id)
                ->where('user_id', $p->user_id)
                ->update(['cards' => $new_hand]);
        }

        $game->update(['deck' => $deck]);
    }
}
