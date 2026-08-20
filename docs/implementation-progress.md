# Implementation Progress

## 1. Database plan

The MVP uses one `users` table for USER, INSTITUTION, and ADMIN accounts. Institution applicants remain USER accounts until approval.

Main tables:

- `users`: authentication, `role`, and `institution_request_status`
- `institution_profiles`: one organization profile per applicant/institution
- `categories`: simple post categories
- `posts`: one table for EVENT and ANNOUNCEMENT content
- `event_participations`: one INTERESTED/GOING state per user and Event
- `saved_posts`: one saved relationship per user and Post
- `files`: existing polymorphic uploads for profiles, categories, institutions, and posts

Important constraints:

- Unique user email
- Unique `institution_profiles.user_id`
- Unique participation pair: `user_id + post_id`
- Composite saved-post key: `user_id + post_id`
- Foreign keys cascade when their parent is deleted

## 2. Models and migrations

Useful generation commands:

```bash
php artisan make:model InstitutionProfile -m
php artisan make:model EventParticipation -m
php artisan make:model SavedPost -m
php artisan make:migration add_role_and_institution_request_status_to_users_table --table=users
php artisan make:migration align_posts_with_events_and_announcements --table=posts
```

`-m` tells Laravel to generate a migration with the model.

Enums were added for:

- `UserRole`: USER, INSTITUTION, ADMIN
- `InstitutionRequestStatus`: NONE, PENDING, APPROVED, REJECTED
- `PostType`: EVENT, ANNOUNCEMENT
- `ParticipationStatus`: INTERESTED, GOING

Run and reverse migrations with:

```bash
php artisan migrate
php artisan migrate:rollback
```

## 3. Relationships

```text
User 1 ── 0..1 InstitutionProfile
User 1 ── many Posts
Category 1 ── many Posts
User many ── many Events through EventParticipation
User many ── many Posts through SavedPost
Models ── polymorphic Files
```

Posts reference `users.id` through `institution_id`. Event-only fields remain nullable because Announcements do not use them.

## 4. Repositories and bindings

Each repository interface defines the available database operations. Its Eloquent implementation performs the queries.

```text
Service → Repository interface → Eloquent repository → Model → Database
```

Post operations currently include create, find by ID, list by category, list by institution, update, and delete.
Post responses also calculate `interested_count`, `going_count`, and `saved_count` from engagement relationships.

Interfaces are bound to implementations in `AppServiceProvider::register()`. Laravel can then inject an interface into a service constructor.

## 5. Authentication and authorization

The role middleware was generated with:

```bash
php artisan make:middleware EnsureUserHasRole
```

It is registered as the `role` alias in `bootstrap/app.php`. A route such as `role:ADMIN` passes `ADMIN` to the middleware; the middleware compares it with the authenticated user's enum role.

The Post policy was generated with:

```bash
php artisan make:policy PostPolicy --model=Post
```

Policy rules:

- Authenticated users may view Posts
- Only approved Institutions may create Posts
- An approved Institution may update/delete its own Posts
- An Admin may update/delete any Post

## 6. Validation

FormRequests now cover authentication, profile updates, categories, files, institution applications/profiles, Posts, and Event participation.

```bash
php artisan make:request Post/UpdatePostRequest
```

`StorePostRequest` requires full creation data. `UpdatePostRequest` uses `sometimes` because PATCH sends only changed fields. Event-only creation fields are excluded from Announcement data.

Actions with no request body—delete, approve, reject, save, and unsave—do not need empty FormRequests.

## 7. Post application flow

```text
Route → auth:api → PostPolicy → FormRequest → PostController
      → PostService → PostRepository → Post model → Database
      → JSON response → React
```

The controller passes the authenticated User, route-bound Category, and validated data to `PostService`. The service adds trusted `institution_id` and `category_id` values before calling the repository.

## 8. Current Post endpoints

All endpoints require `auth:api`.

| Method | Endpoint | Authorization |
|---|---|---|
| GET | `/api/posts?sort=latest\|random` | View all Posts |
| GET | `/api/categories/{category}/posts` | View Posts |
| POST | `/api/categories/{category}/posts` | Approved Institution |
| GET | `/api/institutions/{institution}/posts` | View Posts |
| GET | `/api/categories/{category}/posts/{post}` | View Post |
| PATCH | `/api/categories/{category}/posts/{post}` | Owner Institution or Admin |
| DELETE | `/api/categories/{category}/posts/{post}` | Owner Institution or Admin |
| PUT | `/api/categories/{category}/posts/{post}/participation` | Set Interested/Going |
| DELETE | `/api/categories/{category}/posts/{post}/participation` | Remove participation |
| PUT | `/api/categories/{category}/posts/{post}/save` | Save Post |
| DELETE | `/api/categories/{category}/posts/{post}/save` | Unsave Post |
| GET | `/api/auth/me/activity` | Current user's participation and saved Posts |
| POST | `/api/institution/application` | Submit an Institution application |
| GET | `/api/institution/application` | View the current user's application |
| PATCH | `/api/institution/application` | Update the current user's Institution profile |
| GET | `/api/admin/institution-applications` | Admin lists pending applications |
| PATCH | `/api/admin/institution-applications/{applicant}` | Admin approves or rejects an application |
| GET | `/api/institution/statistics` | Approved Institution, own Posts only |
| GET | `/api/admin/statistics` | Admin platform totals |

## 9. Next work

- Set `ADMIN_PASSWORD` in `.env`, then run `php artisan db:seed` to create/update the initial Admin
- Add viewer-specific `is_saved` and `my_participation_status` fields to Post responses if the UI needs them
- Add focused tests for Posts, Categories, profile updates, participation, saves, and activity
- Add pagination before the amount of Post and activity data becomes large
- Add Filament only after backend workflows are secure
