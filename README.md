# HMCTS App

A task management application built with Laravel 12, Vue.js 3, and Inertia.js.

## Tech Stack

- **Backend:** PHP 8.3+, Laravel 12
- **Frontend:** Vue.js 3, TypeScript, Inertia.js
- **Styling:** Tailwind CSS 4
- **Authentication:** Laravel Fortify
- **Database:** MySQL
- **Build Tool:** Vite

## Requirements

- PHP 8.3 or higher
- Composer
- Node.js & npm
- MySQL or compatible database

## Installation

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Install Node.js dependencies
npm install

# Build frontend assets
npm run build
```

### Default User Credentials

After running the database seeder, you can log in with:

| Field    | Value              |
|----------|--------------------|
| Email    | `test@example.com` |
| Password | `Test@1234`        |

---

## API Documentation

### Tasks

#### Create Task Form

Renders the task creation form.

```
GET /tasks/create
```

**Authentication:** Required

**Response:** Inertia page render (`Tasks/Create`)

---

#### Store Task

Creates a new task.

```
POST /tasks
```

**Authentication:** Required

**Request Headers:**

| Header         | Value                           |
|----------------|----------------------------------|
| Content-Type   | application/json                |
| Accept         | application/json                |
| X-Inertia      | true (for Inertia requests)     |

**Request Body:**

| Field         | Type     | Required | Description                                              |
|---------------|----------|----------|----------------------------------------------------------|
| `title`       | string   | Yes      | Task title (max 255 characters)                          |
| `description` | string   | No       | Task description (can be long text)                      |
| `due`         | string   | Yes      | Due date/time in ISO format `YYYY-MM-DDTHH:mm` (must be today or in the future) |

**Example Request:**

```json
{
    "title": "Complete project documentation",
    "description": "Write comprehensive documentation for the API endpoints",
    "due": "2025-12-15T14:30"
}
```

**Success Response (Inertia):**

Returns an Inertia page render with success message and task details.

```json
{
    "component": "Tasks/Create",
    "props": {
        "success": "Task created successfully.",
        "task": {
            "id": 1,
            "title": "Complete project documentation",
            "description": "Write comprehensive documentation for the API endpoints",
            "due": "2025-12-15T14:30:00",
            "status_id": 1,
            "user_id": 1,
            "created_at": "2025-12-06T10:00:00.000000Z",
            "updated_at": "2025-12-06T10:00:00.000000Z",
            "status": {
                "id": 1,
                "name": "Pending",
                "created_at": "2025-12-05T18:53:36.000000Z",
                "updated_at": "2025-12-05T18:53:36.000000Z"
            },
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    }
}
```

**Validation Errors (422 Unprocessable Entity):**

```json
{
    "message": "The title field is required.",
    "errors": {
        "title": ["The title field is required."],
        "due": ["The due field must be a date after or equal to today."]
    }
}
```

**Validation Rules:**

| Field         | Rules                                                              |
|---------------|--------------------------------------------------------------------|
| `title`       | Required, string, max 255 characters                               |
| `description` | Optional, string                                                   |
| `due`         | Required, format `Y-m-d\TH:i`, must be today or in the future     |

---

## Data Models

### Task

| Field         | Type      | Description                          |
|---------------|-----------|--------------------------------------|
| `id`          | integer   | Primary key                          |
| `title`       | string    | Task title                           |
| `description` | text      | Task description (nullable)          |
| `status_id`   | integer   | Foreign key to statuses table        |
| `user_id`     | integer   | Foreign key to users table           |
| `due`         | datetime  | Due date and time                    |
| `created_at`  | datetime  | Creation timestamp                   |
| `updated_at`  | datetime  | Last update timestamp                |

**Relationships:**
- Belongs to `Status`
- Belongs to `User`

### Status

| Field         | Type      | Description                          |
|---------------|-----------|--------------------------------------|
| `id`          | integer   | Primary key                          |
| `name`        | string    | Status name (e.g., "Pending", "Complete") |
| `created_at`  | datetime  | Creation timestamp                   |
| `updated_at`  | datetime  | Last update timestamp                |

---

## Project Structure

```
hmcts-app/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── TaskController.php    # Task management
│   └── Models/
│       ├── Task.php                  # Task model
│       ├── Status.php                # Status model
│       └── User.php                  # User model
├── database/
│   ├── migrations/                   # Database migrations
│   └── seeders/                      # Database seeders
├── resources/
│   ├── js/                           # Vue.js components
│   └── views/                        # Blade templates
├── routes/
│   ├── web.php                       # Web routes
│   └── settings.php                  # Settings routes
└── tests/                            # Test files
```

---

## Testing

```bash
# Run all tests
composer test

# Or directly with PHPUnit
php artisan test
```
