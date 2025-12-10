# Xeri Card Game – Web API
Project for ADISE 2025–2026  
Author: Your Name  
University of Piraeus

---

## 1. Project Description

This project implements the card game "Ξερή" as a Web API, following the requirements for a 1-person team.  
The game is played entirely through API calls (curl, Postman, browser tools).  
No GUI is required.

The backend is built using:
- PHP (Laravel)
- PostgreSQL database
- JSON communication
- Custom token authentication
- Stateless Web API architecture

All game state (deck, table, hands, captures, scores) is stored in the database.

---

## 2. Game Rules Implemented

- Matching-value capture
- Ξερή (one-card table capture)
- Automatic dealing (6 cards per round)
- Last captor handling (remaining table cards at end)
- Full scoring system:
  - Most cards: +3 points
  - Most diamonds: +1 point
  - Each Ξερή: +10 points
- Turn tracking
- Full deck playthrough
- Game completes only when all cards are played and scored

---

## 3. Database Structure

### users
- id
- email
- token

### games
- id
- status (waiting, active, end, completed)
- creator_id
- deck (json)
- table_cards (json)
- current_player_id
- last_captor_user_id

### game_players
- id
- game_id
- user_id
- player_number
- xeri_count
- score

### hands
- game_id
- user_id
- cards (json)

### captures
- game_id
- user_id
- cards (json)

---

## 4. Authentication

### Login
```
POST /api/login
```

Request:
```json
{
  "email": "player@example.com"
}
```

Response:
```json
{
  "token": "exampleToken123"
}
```

Every request must include:
```
Authorization: Bearer <token>
```

---

## 5. API Endpoints

### 5.1 Create Game
```
POST /api/game/create
Authorization: Bearer <token>
```

Response example:
```json
{
  "game_id": 1,
  "player_number": 1,
  "your_hand": [...],
  "table_cards": [...],
  "message": "Game created. Waiting for opponent."
}
```

A user may not create more than one active game.

---

### 5.2 Join Game
```
POST /api/game/join
Authorization: Bearer <token>
```

Response:
```json
{
  "game_id": 1,
  "player_number": 2,
  "your_hand": [...],
  "table_cards": [...],
  "message": "Joined game"
}
```

Validation:
- cannot join completed game  
- cannot join if already in the game  
- cannot join a full game (2 players)

---

### 5.3 Play a Card
```
POST /api/game/{id}/play
Authorization: Bearer <token>

{
  "card": "10D"
}
```

Examples:

Capture:
```json
{
  "message": "Capture",
  "your_hand": [...],
  "table_cards": []
}
```

Ξερή:
```json
{
  "message": "Ξερή!",
  "table_cards": []
}
```

Normal play:
```json
{
  "message": "Card played"
}
```

Validation:
- must be the player's turn  
- card must be in hand  
- game must not be completed

---

### 5.4 Get Game State
```
GET /api/game/{id}/state
Authorization: Bearer <token>
```

During game:
```json
{
  "game_id": 1,
  "player_number": 1,
  "your_hand": [...],
  "table_cards": [...],
  "opponent_cards": 4,
  "current_player_id": 2,
  "game_status": "active"
}
```

After game ends:
```json
{
  "game_id": 1,
  "results": [...],
  "winner": {...},
  "status": "completed"
}
```

---

### 5.5 Get Final Result
```
GET /api/game/{id}/result
Authorization: Bearer <token>
```

Response:
```json
{
  "game_id": 1,
  "results": [
    { "user_id": 3, "score": 12, "xeri_count": 1 },
    { "user_id": 5, "score": 7, "xeri_count": 0 }
  ],
  "winner": {
    "user_id": 3,
    "score": 12
  },
  "status": "completed"
}
```

---

## 6. Installation

```
composer install
cp .env.example .env
php artisan key:generate

# configure PostgreSQL in .env

php artisan migrate
php -S localhost:8000 -t PUBLIC
```

Server runs at:
```
http://localhost:8000
```

---

## 7. Deployment Link
(To be added if deployed online)

---

## 8. Submission Notes (as required by the assignment)

- Projects must be declared by **26/11/2025**.  
- Final delivery must be committed before **11/1/2026 23:59**.  
- No changes allowed after this timestamp.  
- README must contain:
  - Short project description  
  - Full API documentation  
  - Deployment link (if applicable)  
- Oral exam between **12/1/2026 – 16/1/2026**.

---

## End of README

