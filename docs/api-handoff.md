# API Handoff Guide

This document is the integration contract for the React and Filament teams.

## Status

- Base URL locally: `http://127.0.0.1:8000/api`
- Authentication: JWT Bearer token
- Content type: `application/json`, except file upload
- Roles: `USER`, `INSTITUTION`, `ADMIN`
- Institution request states: `NONE`, `PENDING`, `APPROVED`, `REJECTED`
- Post types: `EVENT`, `ANNOUNCEMENT`
- Participation states: `INTERESTED`, `GOING`

All routes except register, login, forgot-password, and reset-password require:

```http
Authorization: Bearer <token>
Accept: application/json
```

## Local setup

From the `api` directory:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Create the empty SQLite file at `database/database.sqlite`. Then set these values in `.env`:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
ADMIN_NAME="Platform Admin"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD="choose-a-local-password"
```

Finish setup and start the server:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Verification:

```bash
php artisan test
php artisan route:list --path=api
```

The seeded Admin logs in through the normal `/api/auth/login` endpoint.

## Common response objects

### User

```json
{
  "id": 7,
  "name": "Ahmed Amin",
  "email": "ahmed@example.com",
  "email_verified_at": null,
  "role": "USER",
  "institution_request_status": "NONE",
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z"
}
```

`GET /auth/me` also includes these nullable relationships:

```json
{
  "profile_picture": null,
  "institution_profile": null
}
```

An Institution profile has this shape:

```json
{
  "id": 3,
  "user_id": 7,
  "organization_name": "Community Builders",
  "description": "We organize educational events.",
  "website": "https://example.com",
  "contact_email": "contact@example.com",
  "contact_phone": "+201000000000",
  "location": "Cairo",
  "logo": null,
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z"
}
```

### Category

```json
{
  "id": 2,
  "name": "Technology",
  "description": "Technology events and announcements",
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z",
  "image_url": "http://127.0.0.1:8000/storage/uploads/category.png"
}
```

`image_url` is calculated and may be `null`.

### Post

```json
{
  "id": 12,
  "institution_id": 7,
  "category_id": 2,
  "type": "EVENT",
  "title": "Laravel Workshop",
  "description": "A practical workshop.",
  "start_at": "2026-09-01T10:00:00.000000Z",
  "end_at": "2026-09-01T13:00:00.000000Z",
  "location": "Cairo",
  "capacity": 100,
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z",
  "content": "A practical workshop.",
  "interested_count": 8,
  "going_count": 4,
  "saved_count": 6,
  "institution": {},
  "category": {},
  "files": []
}
```

Notes:

- `content` is a temporary legacy alias of `description`.
- Event-only fields are `null` for Announcements.
- Counts are calculated response fields, not columns in `posts`.
- `institution` contains the publishing User and their `institution_profile`.
- Feed responses do not currently contain viewer-specific `is_saved` or `my_participation_status` fields. Use `/auth/me/activity` when needed.

### File

```json
{
  "id": 5,
  "file_path": "uploads/example.png",
  "fileable_id": 7,
  "fileable_type": "App\\Models\\User",
  "user_id": 7,
  "file_name": "example.png",
  "mime_type": "image/png",
  "size_in_kb": 120,
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z",
  "url": "http://127.0.0.1:8000/storage/uploads/example.png"
}
```

`url` is calculated and is the field the UI should display.

## Authentication and profile

### Register

`POST /auth/register` — Public

```json
{
  "name": "Ahmed Amin",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Response `201`:

```json
{
  "message": "User successfully created.",
  "user": {},
  "token": "<jwt>"
}
```

### Login

`POST /auth/login` — Public

```json
{
  "email": "ahmed@example.com",
  "password": "password123"
}
```

Response `200` has `message`, `user`, and `token`.

### Current User

`GET /auth/me` — Authenticated

Returns the User object directly, including `profile_picture` and `institution_profile.logo`. This endpoint does not use the usual `status/data` envelope.

### Update current User

`PATCH /auth/me` — Authenticated

Send either or both fields:

```json
{
  "name": "Updated Name",
  "email": "updated@example.com"
}
```

Response:

```json
{
  "message": "Profile updated successfully.",
  "user": {}
}
```

### Current User activity

`GET /auth/me/activity` — Authenticated

Response:

```json
{
  "status": "success",
  "data": {
    "interested": [],
    "going": [],
    "saved": []
  }
}
```

Each array contains Post objects. Activity Posts include their Institution and Category.

### Refresh token

`POST /auth/refresh` — Authenticated

Response contains a replacement JWT:

```json
{
  "message": "Token refreshed",
  "token": "<new-jwt>"
}
```

The old token becomes invalid.

### Logout

`POST /auth/logout` — Authenticated

Invalidates the current JWT.

### Forgot password

`POST /auth/forgot-password` — Public

```json
{
  "email": "ahmed@example.com"
}
```

Mail transport must be configured locally for delivery.

### Reset password

`POST /auth/reset-password` — Public

```json
{
  "token": "<reset-token>",
  "email": "ahmed@example.com",
  "password": "new-password123",
  "password_confirmation": "new-password123"
}
```

## Institution applications

### Submit application

`POST /institution/application` — Authenticated regular User

```json
{
  "organization_name": "Community Builders",
  "description": "We organize educational events.",
  "website": "https://example.com",
  "contact_email": "contact@example.com",
  "contact_phone": "+201000000000",
  "location": "Cairo"
}
```

Required: `organization_name`, `description`. Other fields are nullable.

Response `201` contains the User with:

```json
{
  "role": "USER",
  "institution_request_status": "PENDING",
  "institution_profile": {}
}
```

Rejected Users may edit their existing profile and reapply. Pending or approved Users cannot submit another active application.

### View own application

`GET /institution/application` — Authenticated

Returns the current User with `institution_profile.logo`. Returns `404` when no profile exists.

### Update own Institution profile

`PATCH /institution/application` — Authenticated applicant/Institution

Send any subset of the application fields. This changes organization information only; it does not change role or request status.

### Admin list pending applications

`GET /admin/institution-applications` — Admin only

Response:

```json
{
  "status": "success",
  "data": [
    {
      "id": 7,
      "role": "USER",
      "institution_request_status": "PENDING",
      "institution_profile": {}
    }
  ]
}
```

### Admin review application

`PATCH /admin/institution-applications/{applicant}` — Admin only

`{applicant}` is the applicant's User ID.

```json
{
  "status": "APPROVED"
}
```

or:

```json
{
  "status": "REJECTED"
}
```

Approval changes the role to `INSTITUTION`. Rejection keeps the role as `USER`. Only a currently `PENDING` application can be reviewed.

## Categories

| Method | Endpoint | Purpose | Access |
|---|---|---|---|
| GET | `/categories` | List Categories | Authenticated |
| GET | `/categories/{category}` | Get one Category | Authenticated |
| POST | `/categories` | Create Category | Admin |
| PATCH | `/categories/{category}` | Partially update Category | Admin |
| DELETE | `/categories/{category}` | Delete Category | Admin |

Create payload:

```json
{
  "name": "Technology",
  "description": "Technology events and announcements"
}
```

Update accepts either field. Category responses use the standard `status`, optional `message`, and `data` envelope.

## Posts

| Method | Endpoint | Purpose | Access |
|---|---|---|---|
| GET | `/posts?sort=latest` | General Post feed | Authenticated |
| GET | `/posts?sort=random` | Randomized Post feed | Authenticated |
| GET | `/institutions/{institution}/posts` | Posts from one Institution User ID | Authenticated |
| GET | `/categories/{category}/posts` | Posts in a Category | Authenticated |
| POST | `/categories/{category}/posts` | Create in Category | Approved Institution |
| GET | `/categories/{category}/posts/{post}` | Get one scoped Post | Authenticated |
| PATCH | `/categories/{category}/posts/{post}` | Partially update Post | Owner Institution or Admin |
| DELETE | `/categories/{category}/posts/{post}` | Delete Post | Owner Institution or Admin |

`{category}` and `{post}` are IDs. Scoped binding returns `404` when the Post does not belong to the Category in the URL.

Create Event:

```json
{
  "type": "EVENT",
  "title": "Laravel Workshop",
  "description": "A practical workshop.",
  "start_at": "2026-09-01 10:00:00",
  "end_at": "2026-09-01 13:00:00",
  "location": "Cairo",
  "capacity": 100
}
```

Create Announcement:

```json
{
  "type": "ANNOUNCEMENT",
  "title": "Registration is open",
  "description": "Applications close next week."
}
```

Do not send Event-only fields for an Announcement. The Category ID comes from the URL and `institution_id` comes from the authenticated User; neither belongs in the payload.

Update accepts any subset of:

```text
title, description, start_at, end_at, location, capacity
```

Post type and Category are not currently editable.

## Engagement

### Interested or Going

`PUT /categories/{category}/posts/{post}/participation` — Authenticated

```json
{
  "status": "INTERESTED"
}
```

or:

```json
{
  "status": "GOING"
}
```

This is idempotent: it creates the User/Post row or replaces its status. It works only for `EVENT` Posts.

Response data:

```json
{
  "id": 4,
  "user_id": 7,
  "post_id": 12,
  "status": "GOING",
  "created_at": "2026-08-20T08:00:00.000000Z",
  "updated_at": "2026-08-20T08:00:00.000000Z"
}
```

`DELETE /categories/{category}/posts/{post}/participation` removes the current User's state.

### Save or unsave Post

`PUT /categories/{category}/posts/{post}/save` saves the Post idempotently.

`DELETE /categories/{category}/posts/{post}/save` removes the saved relationship.

Both require authentication and return a success message without a data object.

## Statistics

### Institution statistics

`GET /institution/statistics` — Institution role; service also requires approval

```json
{
  "status": "success",
  "data": {
    "posts_count": 10,
    "events_count": 6,
    "announcements_count": 4,
    "interested_count": 30,
    "going_count": 12,
    "saved_count": 18
  }
}
```

Counts include only Posts owned by the current Institution.

### Admin statistics

`GET /admin/statistics` — Admin only

```json
{
  "status": "success",
  "data": {
    "users_count": 100,
    "institutions_count": 8,
    "posts_count": 50,
    "events_count": 30,
    "announcements_count": 20,
    "interested_count": 200,
    "going_count": 90,
    "saved_count": 140,
    "pending_institution_requests_count": 3
  }
}
```

## Files and images

### List own uploads

`GET /drive` — Authenticated

Returns files whose `user_id` is the current User.

### Upload and attach

`POST /drive/upload` — Authenticated, `multipart/form-data`

| Field | Value |
|---|---|
| `file` | File, maximum 10 MB |
| `fileable_id` | Target model ID |
| `fileable_type` | One allowed PHP model class below |

Allowed targets:

```text
App\Models\User                Current User profile picture
App\Models\InstitutionProfile Owned Institution logo
App\Models\Category            Admin-managed Category image
App\Models\Post                Owned Institution Post attachment
```

Example profile picture form fields:

```text
file=<binary>
fileable_id=7
fileable_type=App\Models\User
```

The API resolves the target model and runs its `attachFile` policy. A User cannot attach files to another User, Institution, Category, or Post without permission.

### Delete upload

`DELETE /drive/{id}` — Authenticated

Deletes the database record and physical file only when it was uploaded by the current User.

## Error responses

| Status | Meaning |
|---|---|
| `401` | Missing, invalid, or expired JWT |
| `403` | Authenticated but role/policy denied |
| `404` | Route-bound model or requested record not found |
| `422` | Request validation or business-state failure |
| `500` | Unexpected server failure |

Typical `422`:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Readable validation message."]
  }
}
```

## Filament handoff

Filament is not installed in this repository yet. The backend behavior that an Admin dashboard needs is already available through:

- Admin JWT login
- Admin statistics
- Pending Institution application list and review
- Category CRUD through `CategoryPolicy`
- Post update/delete moderation through `PostPolicy`
- Existing Eloquent models and relationships

The Filament developer should reuse the existing models, enums, policies, and service business rules rather than creating a second approval system. They must confirm the selected Filament version supports the project's Laravel version before installation.

## Current integration notes

The API is ready for local frontend and dashboard integration. Before calling it production-ready, complete these items:

- Add feature tests for Auth, Categories, Posts, participation, saves, and file authorization.
- Add pagination before real datasets become large.
- Decide whether Post feeds require viewer-specific `is_saved` and `my_participation_status` fields.
- Configure real mail delivery for password reset.
- Standardize response envelopes if the frontend team requires one universal format.
- Install and test Filament separately; it is not currently a dependency.
