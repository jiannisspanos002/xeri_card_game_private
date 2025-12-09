<?php

namespace App\Helpers;

use App\Models\Capture;

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

        return [
            'table' => [],
            'captures' => true,
            'xeri' => $xeri
        ];
    }
}
