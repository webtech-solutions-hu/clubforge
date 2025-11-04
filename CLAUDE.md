# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Club Forge is a Laravel 12 application with Filament 3.3 admin panel. It uses:
- **Backend**: PHP 8.2+, Laravel 12, Filament 3.3 (admin panel framework)
- **Frontend**: Vite 7, Tailwind CSS 4, Alpine.js 3.15
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum with Google reCAPTCHA v2
- **Queue System**: Database-backed queues
- **Environment**: Rewardenv Laravel Docker environment

## Docker Environment

This project runs in a **Rewardenv Laravel docker environment**. All terminal commands must be prefixed with `docker exec clubforge-php-fpm-1`:

```bash
# Example: Running artisan commands
docker exec clubforge-php-fpm-1 php artisan migrate

# Example: Running composer commands
docker exec clubforge-php-fpm-1 composer install

# Example: Running tests
docker exec clubforge-php-fpm-1 php artisan test
```

**Available containers**:
- `clubforge-php-fpm-1` - PHP-FPM container (primary)
- `clubforge-nginx-1` - Nginx web server
- `clubforge-db-1` - Database container
- `clubforge-valkey-1` - Valkey (Redis) cache
- `clubforge-php-debug-1` - PHP debug container

## Development Commands

### Initial Setup
```bash
docker exec clubforge-php-fpm-1 composer setup
# This runs: composer install, creates .env, generates key, runs migrations, npm install, npm run build
```

### Development Server
```bash
docker exec clubforge-php-fpm-1 composer dev
# Starts 4 concurrent processes:
# - Laravel dev server (port 8000)
# - Queue worker
# - Log viewer (pail)
# - Vite dev server (hot reload)
```

### Testing
```bash
docker exec clubforge-php-fpm-1 composer test
# Clears config and runs PHPUnit test suite
```

To run a single test:
```bash
docker exec clubforge-php-fpm-1 php artisan test --filter=TestMethodName
# Or for a specific file:
docker exec clubforge-php-fpm-1 php artisan test tests/Feature/ExampleTest.php
```

### Code Quality
```bash
docker exec clubforge-php-fpm-1 vendor/bin/pint
# Laravel Pint (code formatting)
```

### Database
```bash
docker exec clubforge-php-fpm-1 php artisan migrate
# Run migrations

docker exec clubforge-php-fpm-1 php artisan migrate:fresh --seed
# Fresh migration with seeders

docker exec clubforge-php-fpm-1 php artisan tinker
# Laravel REPL for testing code
```

### Frontend
```bash
npm run dev
# Start Vite dev server (hot reload) - runs on host, not in container

npm run build
# Build production assets
```

## Architecture

### Filament Admin Panel Structure
The application uses Filament for its admin interface, configured in `app/Providers/Filament/AdminPanelProvider.php`:
- **Panel ID**: `admin`
- **Path**: `/admin`
- **Resources**: Auto-discovered from `app/Filament/Admin/Resources/`
- **Pages**: Auto-discovered from `app/Filament/Admin/Pages/`
- **Widgets**: Auto-discovered from `app/Filament/Admin/Widgets/`
- **Primary Color**: Amber
- **Footer**: Displays creator info (Webtech-Solutions) and version information

#### Versioning System
The application uses a semantic versioning system configured in `config/version.php`:
- **Version Format**: `v{major}.{minor}.{patch}-{stage}.{stage_version}`
- **Stages**: `alpha` (red badge), `beta` (yellow badge), `stable` (green badge)
- **Current Version**: Displayed in admin footer
- **Configuration**: `config/version.php` - Update version numbers and stage here
- **Example**: `v1.0.0-alpha.1` or `v1.2.3` (stable versions omit stage suffix)
- **Footer Component**: `resources/views/components/admin-footer.blade.php`

#### Authentication
Filament authentication is enhanced with Google reCAPTCHA v2 protection:
- **Login**: `/admin/login` - Protected with reCAPTCHA
- **Register**: `/admin/register` - Protected with reCAPTCHA
- **Password Reset**: `/admin/password-reset/request` - Protected with reCAPTCHA
- **Email Verification**: `/admin/email-verification/prompt`
- **Profile**: `/admin/profile`

**reCAPTCHA Configuration**:
- Set `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` in `.env`
- Set `RECAPTCHA_ENABLED=true` to enable (default)
- Set `RECAPTCHA_ENABLED=false` to disable for development
- Custom auth pages: `app/Filament/Admin/Pages/Auth/`
  - `Login.php` - Custom login with reCAPTCHA
  - `Register.php` - Custom registration with reCAPTCHA (assigns Guest role)
  - `RequestPasswordReset.php` - Custom password reset with reCAPTCHA
- Validation rule: `app/Rules/RecaptchaRule.php`

Default test credentials (from seeder):
- Email: `admin@example.com`
- Password: `password`

When creating Filament resources, pages, or widgets, place them in the appropriate `app/Filament/Admin/` subdirectory and they will be auto-discovered.

#### Navigation Structure
The admin panel is organized into navigation groups with badge counters showing record counts:

**Club Wall** (`navigationGroup: 'Club Wall'`):
- **Message Board** (`/admin/posts`) - Badge shows total post count
  - Create posts with optional title and image
  - Comment on posts via relation manager
  - Like/react to posts
  - Pin important posts (admin only)
  - Filter by pinned status and author
  - View comment and like counts
  - Delete own posts or moderate as admin
  - Images stored in `storage/app/public/posts/`
  - Supports slide-over view action

- **Events** (`/admin/events`) - Badge shows upcoming events count
  - Event types: Board Game, RPG Session, Tournament, Workshop, Social Event
  - Event statuses: Upcoming, Ongoing, Completed, Cancelled
  - Participant management with roles (player, game master, etc.)
  - Result tracking for completed events
  - Join/leave functionality with max participant limits
  - Event images stored in `storage/app/public/events/`
  - Only administrators, game masters, and owners can create events
  - Two relation managers: Participants and Results

**System Resources** (`navigationGroup: 'System Resources'`):
- **Users** (`/admin/users`) - User management
  - View all users with their assigned roles and avatars
  - Assign multiple roles to users
  - Filter by role and email verification status
  - View supervisor status indicator
  - Upload and manage user avatars (stored in `storage/app/public/avatars/`)

- **Roles** (`/admin/roles`) - Manage user roles and permissions
  - Five default roles: Administrator, Owner, Game Master, Member, Guest
  - Supervisor capability for Administrator role (full system access)
  - Create custom roles with supervisor privileges
  - View users assigned to each role

- **Audit Logs** (`/admin/audit-logs`) - Badge shows total audit log count
  - Read-only system activity log
  - Tracks events: login, roles_changed, event_created/updated/deleted, participant actions, result actions
  - Filter by event type, user, and date range
  - View-only access (no create/edit/delete)
  - **Administrator access only**
  - Stores user agent, IP address, and event properties

- **Sessions** (`/admin/sessions`) - View and manage user sessions
  - View all active and expired sessions
  - Filter by active sessions, authenticated users, or guests
  - Terminate individual sessions or bulk terminate
  - Clear expired sessions (older than 2 hours)
  - View session details (ID, user, IP address, user agent, last activity)

**Dashboard**:
- Default landing page with AccountWidget
- Custom widgets auto-discovered from `app/Filament/Admin/Widgets/`
- Includes: UpcomingEvents, MyResults, ClubStats

**Notifications**:
- Notification bell in user menu (via `PanelsRenderHook::USER_MENU_BEFORE`)
- Livewire component: `notification-bell`
- Notification model with read/unread status tracking
- Support for icons, colors, action URLs, and custom data

#### Role System
The application implements a flexible role-based access control system:

**Default Roles**:
- **Administrator**: Supervisor role with full system access (default supervisor)
- **Owner**: Organization owner with management capabilities
- **Game Master**: Manages games and gaming activities
- **Member**: Regular member with standard access
- **Guest**: Limited access for new or unverified users (assigned on registration)

**Key Features**:
- Users can have multiple roles simultaneously
- Supervisor roles have access to everything (Administrator is supervisor by default)
- New user registrations automatically receive the Guest role
- Roles are managed through the Roles resource in System Monitoring

**Helper Methods** (in `User` model):
- `hasRole(string|array $roles)`: Check if user has specific role(s)
- `hasAnyRole(array $roles)`: Check if user has any of the specified roles
- `isSupervisor()`: Check if user has any supervisor role
- `assignRole(string|Role $role)`: Assign a role to user
- `removeRole(string|Role $role)`: Remove a role from user

### Application Structure
- **Models**: `app/Models/` - Core models include:
  - `User` - Extended with role system methods, avatar support, and notifications
  - `Role`, `Permission` - Role-based access control
  - `Post`, `Comment`, `Like` - Message board functionality
  - `Event`, `Result` - Event management and participant tracking
  - `Notification` - In-app notification system
  - `Session` - Database-backed session management
  - `AuditLog` - System activity auditing
- **Filament Structure**:
  - `app/Filament/Admin/Resources/` - Auto-discovered resource files
  - `app/Filament/Admin/Pages/` - Custom pages (Profile, MyEvents, Members)
  - `app/Filament/Admin/Widgets/` - Dashboard widgets
  - `app/Filament/Admin/Resources/*/RelationManagers/` - Relation managers for nested data
  - `app/Filament/AvatarProviders/` - Custom avatar provider (`UserAvatarProvider`)
- **Standard Laravel**:
  - `app/Http/Controllers/` - HTTP controllers
  - `app/Providers/` - Service providers including `Filament/AdminPanelProvider.php`
  - `database/migrations/` - Database migrations
  - `database/factories/` - Model factories for testing
  - `database/seeders/` - Database seeders
  - `resources/views/` - Blade templates
  - `resources/css/` and `resources/js/` - Frontend assets compiled by Vite
- **File Storage**: `storage/app/public/` - Public file storage (linked to `public/storage`)
  - `avatars/` - User avatar images
  - `posts/` - Message board post images
  - `events/` - Event images

### Configuration
- Default database: SQLite (`database/database.sqlite`)
- Queue connection: Database
- Cache store: Database
- Session driver: Database
- Mail: Log (development)

### Testing Setup
PHPUnit is configured to use:
- In-memory SQLite database
- Array cache driver
- Sync queue connection
- Array session driver

### Filament Conventions
When working with Filament resources in this project:
- **View Actions**: Use slide-over modals by default (`.slideOver()`)
- **Navigation Badges**: All resources implement `getNavigationBadge()` to show record counts
- **Navigation Groups**: Organize related resources together
- **Action Buttons**: Use icon-only actions in tables (`.label('')`) with icons positioned before columns
- **Custom Avatar Provider**: `UserAvatarProvider` handles user avatar display throughout the panel
- **Role-Based Visibility**: Use `canAccess()`, `canCreate()`, `canEdit()`, `canDelete()` for role-based permissions