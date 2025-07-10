# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application using Inertia.js with Vue 3 and Tailwind CSS. The project follows a modular architecture pattern with a custom module system for better code organization and maintainability.

## Development Commands

### Backend
- `composer dev` - Start full development environment (Laravel server, queue worker, logs, and Vite)
- `composer test` - Run PHPUnit tests with configuration cleanup
- `php artisan serve` - Start Laravel development server
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - Start log monitoring
- `php artisan tinker` - Open Laravel REPL
- `php artisan make:command {name}` - Create artisan command

### Frontend
- `npm run dev` - Start Vite development server
- `npm run build` - Build production assets

### Testing
- `composer test` - Run all tests
- `php artisan test` - Run tests directly
- `php artisan test --filter {TestName}` - Run specific test

### Code Quality
- `vendor/bin/pint` - Run Laravel Pint for code formatting

## Architecture Overview

### Modular Structure
The application uses a custom modular architecture where each module is self-contained:

```
app/Modules/{ModuleName}/
├── Controllers/
├── Models/
├── Services/
├── Repositories/
├── DTOs/
├── Actions/
├── Requests/
├── Exceptions/
├── Panel/Controllers/    # Admin panel controllers
├── Web/Controllers/      # Frontend controllers
├── Panel/routes.php      # Admin routes
├── Web/routes.php        # Frontend routes
└── {ModuleName}ServiceProvider.php
```

### Key Modules
- **User** - User management, roles, permissions
- **Dashboard** - Admin dashboard functionality
- **Settings** - Application and mail settings
- **ActivityLog** - Activity tracking and audit logs
- **MailNotification** - Email notifications and logs

### Frontend Architecture
- **Vue 3** with Composition API
- **Inertia.js** for SPA-like experience
- **Tailwind CSS** for styling
- **Vite** for asset bundling

### Frontend Structure
```
resources/js/
├── Components/
│   ├── Panel/          # Generic admin panel components
│   ├── Shared/         # Jetstream shared components
│   └── Web/           # Generic frontend components
├── Layouts/
│   ├── AppLayout.vue   # Main app layout
│   ├── PanelLayout.vue # Admin panel layout
│   └── GuestLayout.vue # Guest layout
├── Modules/           # Module-specific Vue components
│   └── {ModuleName}/
│       ├── Panel/
│       │   ├── Components/  # Module admin components
│       │   ├── Index.vue
│       │   ├── Show.vue
│       │   ├── Create.vue
│       │   └── Edit.vue
│       ├── Web/
│       │   └── Components/  # Module frontend components
│       ├── Shared/
│       │   └── components/  # Module shared components
│       └── routes.js
├── Pages/             # Page components
├── Composables/       # Vue composables
└── app.js            # Main entry point
```

## Development Patterns

### Module Development
When creating new modules:
1. Create module directory in `app/Modules/{ModuleName}`
2. Follow the standard module structure
3. Create ServiceProvider to register module services
4. Register the ServiceProvider in `config/app.php`
5. Use the ModuleLoader helper for route loading

### Service Layer Pattern
Each module follows the service-repository pattern:
- **Controllers** handle HTTP requests and extend `BaseController`
- **Services** contain business logic and use `TransactionTrait`
- **Repositories** handle data access and extend `BaseRepository`
- **DTOs** for data transfer with consistent typing
- **Actions** for specific operations
- **Exceptions** extend `BaseException` for consistent error handling

### Base Classes Usage
- **Services**: Use `TransactionTrait` for consistent transaction handling
- **Repositories**: Extend `BaseRepository` and implement `BaseRepositoryInterface`
- **Controllers**: Extend `BaseController` for consistent response patterns
- **Exceptions**: Extend `BaseException` for structured error handling

### Frontend Patterns
- Use composables for reusable logic
- Components should be modular and reusable
- Follow Vue 3 Composition API patterns
- Use Inertia.js for page navigation
- Module-specific components go in `/Modules/{ModuleName}/Panel/Components/`
- Generic reusable components go in `/Components/Panel/` or `/Components/Shared/`
- Follow PascalCase naming convention for all Vue files
- Use consistent component suffixes: List, ListRow, Card, Form

## Database

- **SQLite** for development (located at `database/database.sqlite`)
- **MySQL/PostgreSQL** for production
- Uses Laravel migrations and seeders

## Authentication & Authorization

- **Laravel Jetstream** for authentication
- **Spatie Laravel Permission** for roles and permissions
- **Laravel Sanctum** for API authentication
- Custom middleware for user security

## Key Technologies

- **Laravel 12** - PHP framework
- **Vue 3** - Frontend framework
- **Inertia.js** - Modern monolith approach
- **Tailwind CSS** - Utility-first CSS
- **Vite** - Build tool
- **SQLite/MySQL** - Database
- **Laravel Jetstream** - Authentication scaffolding
- **Spatie Laravel Permission** - Role/permission management
- **Spatie Laravel ActivityLog** - Activity logging

## Environment Setup

1. Copy `.env.example` to `.env`
2. Run `php artisan key:generate`
3. Create SQLite database: `touch database/database.sqlite`
4. Run migrations: `php artisan migrate`
5. Install dependencies: `composer install && npm install`
6. Start development: `composer dev`

## Code Style

- Follow Laravel conventions
- Use type hints for PHP 8.2+
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Component names should be PascalCase
- Use meaningful variable and method names

## Coding Standards

### Service Layer Standards
- Always use `TransactionTrait` for database operations
- Implement consistent constructor dependency injection
- Use `executeInTransaction()`, `updateInTransaction()`, `deleteInTransaction()` methods
- Follow consistent error handling patterns

### Repository Layer Standards
- Extend `BaseRepository` for common functionality
- Implement `BaseRepositoryInterface` 
- Use consistent method naming: `getAll()`, `getAllPaginated()`, `findById()`, `findByIdOrFail()`
- Return consistent types: Collection, Model, or LengthAwarePaginator

### Controller Layer Standards
- Extend `BaseController` for common response methods
- Use `successResponse()`, `errorResponse()`, `jsonSuccessResponse()` for consistent responses
- Handle exceptions with `handleException()` or `handleCustomException()`
- Use Form Request classes for validation

### Exception Standards
- Extend `BaseException` for structured error handling
- Include error codes and context data
- Use static factory methods for common exceptions

### Frontend Standards
- Module-specific components in `{Module}/Panel/Components/`
- Use relative imports for module components: `./Components/ComponentName.vue`
- Follow Vue 3 Composition API patterns consistently