# API Documentation

## Authentication

All endpoints except `/api/register` and `/api/login` require Bearer token authentication.

```
Authorization: Bearer {token}
```

## Endpoints

### Auth

#### POST /api/register
Register a new user.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "student"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "student"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

#### POST /api/login
Authenticate user.

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Chat

#### POST /api/chat
Send message to AI tutor.

**Request:**
```json
{
  "knowledge_base_id": 1,
  "message": "Explain quantum physics",
  "mode": "tutor",
  "conversation_id": null
}
```

**Response:**
```json
{
  "response": "Quantum physics is the study of matter and energy at the most fundamental level...",
  "conversation_id": 123,
  "tokens_used": 150
}
```

### Challenges

#### GET /api/challenge/daily
Get today's challenge status.

**Response:**
```json
{
  "has_challenge_today": true,
  "challenge_id": 456,
  "is_answered": false,
  "type": "quiz",
  "title": "Physics Basics",
  "current_streak": 5,
  "total_points": 250
}
```

#### POST /api/challenge/answer
Submit challenge answer.

**Request:**
```json
{
  "challenge_id": 456,
  "answer": "Option B"
}
```

**Response:**
```json
{
  "is_correct": true,
  "points_earned": 10,
  "correct_answer": "Option B",
  "explanation": "This is correct because...",
  "total_points": 260,
  "current_streak": 6
}
```

## Error Responses

```json
{
  "message": "Error description",
  "errors": {
    "field": ["error message"]
  }
}
```

Status codes:
- `200` - Success
- `201` - Created
- `401` - Unauthorized
- `422` - Validation Error
- `500` - Server Error
