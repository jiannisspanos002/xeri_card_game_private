<?php

namespace App\Http\Controllers;

use App\Helpers\CardHelper;
use App\Http\Resources\BaseResource;
use App\Http\Resources\GameBaseResource;
use App\Http\Resources\GameStateResource;
use App\Models\Capture;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Hand;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->auth_user;

        $deck = $this->createDeck();
        shuffle($deck);

        $table_cards = array_slice($deck, 0, 4);

        $deck = array_slice($deck, 4);

        $hand = array_slice($deck, 0, 6);
        $deck = array_slice($deck, 6);

        $game = Game::create([
            'creator_id' => $user->id,
            'status' => 'waiting',
            'current_player_id' => $user->id,
            'deck' => $deck,
            'table_cards' => $table_cards,
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'player_number' => 1,
        ]);

        Hand::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'cards' => $hand,
        ]);

        return (new GameBaseResource([
            'game_id' => $game->id,
            'player_number' => 1,
            'your_hand' => $hand,
            'table_cards' => $table_cards
        ]))->withMessage("Game created")->withStatusCode(200);
    }

    private function createDeck()
    {
        $suits = ['H', 'D', 'C', 'S'];
        $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

        $deck = [];
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = $value . $suit;
            }
        }

        return $deck;
    }

    public function join(Request $request)
    {
        $user = $request->auth_user;

        $game = Game::where('status', 'waiting')
            ->whereDoesntHave('players', function ($game) use ($user) {
                $game->where('user_id', $user->id);
            })
            ->first();

        if (!$game) {
            return (new BaseResource([]))
                ->withMessage("No available games to join")
                ->withStatusCode(404);
        }

        if ($game->players()->count() >= 2) {
            return (new BaseResource([]))
                ->withMessage("Game is already full")
                ->withStatusCode(400);
        }

        $deck = $game->deck;
        $hand = array_slice($deck, 0, 6);
        $deck = array_slice($deck, 6);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'player_number' => 2,
        ]);

        Hand::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'cards' => $hand,
        ]);

        $game->update([
            'status' => 'active',
            'deck' => $deck
        ]);

        $resourceData = [
            'game_id' => $game->id,
            'player_number' => 2,
            'your_hand' => $hand,
            'table_cards' => $game->table_cards,
        ];

        return (new GameBaseResource($resourceData))
            ->withMessage("Joined game successfully")->withStatusCode(200);
    }

    public function state(Request $request, Game $game)
    {
        $user = $request->auth_user;
        $game_id = $game->id;

        if (!$game) {
            return (new BaseResource(null))
                ->withMessage("Game not found")
                ->withStatusCode(404);
        }

        $player = $game->players()->where('user_id', $user->id)->first();

        if (!$player) {
            return (new BaseResource(null))
                ->withMessage("You are not part of this game")
                ->withStatusCode(403);
        }

        $hand = Hand::where('game_id', $game_id)
            ->where('user_id', $user->id)
            ->first()
            ->cards;

        $opponent = $game->players()->where('user_id', '!=', $user->id)->first();

        $opponent_cards = $opponent
            ? Hand::where('game_id', $game_id)
            ->where('user_id', $opponent->user_id)
            ->first()
            ?->cards
            : [];

        $opponent_count = is_array($opponent_cards) ? count($opponent_cards) : 0;

        $data = [
            'game_id' => $game->id,
            'player_number' => $player->player_number,
            'your_hand' => $hand,
            'table_cards' => $game->table_cards,
            'opponent_cards' => $opponent_count,
            'current_player_id' => $game->current_player_id,
            'status' => $game->status,
        ];

        return (new GameStateResource($data))
            ->withMessage("Game state retrieved");
    }

    public function play(Request $request, Game $game)
    {
        $user = $request->auth_user;
        $game_id = $game->id;

        $request->validate([
            'card' => 'required|string'
        ]);

        $card = $request->card;

        if (!$game) {
            return (new BaseResource(null))
                ->withMessage('Game not found')
                ->withStatusCode(404);
        }

        $player = $game->players()->where('user_id', $user->id)->first();

        if (!$player) {
            return (new BaseResource(null))
                ->withMessage('You are not part of this game')
                ->withStatusCode(403);
        }

        if ($game->current_player_id !== $user->id) {
            return (new BaseResource(null))
                ->withMessage('Not your turn')
                ->withStatusCode(409);
        }

        $hand_model = Hand::where('game_id', $game_id)
            ->where('user_id', $user->id)
            ->first();

        $hand = $hand_model->cards;

        if (!in_array($card, $hand)) {
            return (new BaseResource(null))
                ->withMessage('You do not have that card')
                ->withStatusCode(422);
        }

        $table = $game->table_cards;

        $result = CardHelper::applyCapture($card, $table, $game_id, $user->id);

        $table   = $result['table'];
        $capture = $result['captures'];
        $xerii   = $result['xeri'];


        $hand = array_values(array_diff($hand, [$card]));
        $hand_model->update(['cards' => $hand]);

        $game->update(['table_cards' => $table]);

        $opponent = $game->players()
            ->where('user_id', '!=', $user->id)
            ->first();

        if ($opponent) {
            $game->update(['current_player_id' => $opponent->user_id]);
        }

        $stateData = [
            'game_id' => $game->id,
            'player_number' => $player->player_number,
            'your_hand' => $hand,
            'table_cards' => $table,
            'opponent_cards' => 6,
            'current_player_id' => $game->current_player_id,
            'status' => $game->status
        ];

        $message = $capture
            ? ($xerii ? "Ξερή!" : "Capture!")
            : "Card played";

        return (new GameStateResource($stateData))
            ->withMessage($message);
    }
}
