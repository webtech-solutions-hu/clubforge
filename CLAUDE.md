# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Club Forge is a Laravel 12 application with Filament 3.3 admin panel. It uses:
- **Backend**: PHP 8.2+, Laravel 12, Filament 3.3 (admin panel framework)
- **Frontend**: Vite 7, Tailwind CSS 4
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **Queue System**: Database-backed queues

## Development Commands

### Initial Setup
```bash
composer setup
# This runs: composer install, creates .env, generates key, runs migrations, npm install, npm run build
```

### Development Server
```bash
composer dev
# Starts 4 concurrent processes:
# - Laravel dev server (port 8000)
# - Queue worker
# - Log viewer (pail)
# - Vite dev server (hot reload)
```

### Testing
```bash
composer test
# Clears config and runs PHPUnit test suite
```

To run a single test:
```bash
php artisan test --filter=TestMethodName
# Or for a specific file:
php artisan test tests/Feature/ExampleTest.php
```

### Code Quality
```bash
vendor/bin/pint
# Laravel Pint (code formatting)
```

### Database
```bash
php artisan migrate
# Run migrations

php artisan migrate:fresh --seed
# Fresh migration with seeders

php artisan tinker
# Laravel REPL for testing code
```

### Frontend
```bash
npm run dev
# Start Vite dev server (hot reload)

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

#### Authentication
Filament provides built-in authentication pages:
- **Login**: `/admin/login`
- **Register**: `/admin/register`
- **Password Reset**: `/admin/password-reset/request`
- **Email Verification**: `/admin/email-verification/prompt`
- **Profile**: `/admin/profile`

Default test credentials (from seeder):
- Email: `admin@example.com`
- Password: `password`

When creating Filament resources, pages, or widgets, place them in the appropriate `app/Filament/Admin/` subdirectory and they will be auto-discovered.

#### Admin Features
The admin panel includes the following features:

**System Monitoring**:
- **Sessions** (`/admin/sessions`): View and manage user sessions
  - View all active and expired sessions
  - Filter by active sessions, authenticated users, or guests
  - Terminate individual sessions or bulk terminate
  - Clear expired sessions (older than 2 hours)
  - View session details (ID, user, IP address, user agent, last activity)

- **Roles** (`/admin/roles`): Manage user roles and permissions
  - Five default roles: Administrator, Owner, Game Master, Member, Guest
  - Supervisor capability for Administrator role (full system access)
  - Create custom roles with supervisor privileges
  - View users assigned to each role

- **Users** (`/admin/users`): User management
  - View all users with their assigned roles
  - Assign multiple roles to users
  - Filter by role and email verification status
  - View supervisor status indicator

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
- **Models**: `app/Models/` - Eloquent models
- **Controllers**: `app/Http/Controllers/` - HTTP controllers
- **Providers**: `app/Providers/` - Service providers
- **Migrations**: `database/migrations/` - Database migrations
- **Factories**: `database/factories/` - Model factories for testing
- **Seeders**: `database/seeders/` - Database seeders
- **Views**: `resources/views/` - Blade templates
- **Frontend Assets**: `resources/css/` and `resources/js/` - Compiled by Vite

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
- Project running in Rewardenv Laravel docker environment locally.
- Terminal commands start with docker exec clubforge-php-fpm-1
- Filament view action default modal slide over.