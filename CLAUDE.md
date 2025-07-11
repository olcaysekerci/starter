# CLAUDE.md

This file provides comprehensive guidance to Claude Code (claude.ai/code) when working with this Laravel project.

## 📋 Project Overview

This is a **Laravel 12 Starter Kit** featuring a modern, modular architecture designed for scalable web applications. The project combines **Laravel 12** backend with **Vue 3 + Inertia.js** frontend, implementing industry best practices for enterprise-level applications.

### 🎯 Project Purpose
A comprehensive starter template for building modern web applications with user management, role-based permissions, activity logging, mail notifications, and a full-featured admin panel.

### 🏗️ Architecture Philosophy
- **Modular Design**: Each feature is organized into self-contained modules
- **Service-Repository Pattern**: Clear separation of business logic and data access
- **Component-Based Frontend**: Reusable Vue 3 components with Composition API
- **Security-First Approach**: Built-in authentication, authorization, and audit trails

## 🚀 Development Environment

### Quick Start Commands

#### Full Development Environment (Recommended)
```bash
composer dev  # Starts Laravel server, queue worker, log monitoring, and Vite
```

#### Individual Services
```bash
# Backend
php artisan serve                    # Laravel development server (http://localhost:8000)
php artisan queue:listen --tries=1   # Queue worker for background jobs
php artisan pail --timeout=0         # Real-time log monitoring

# Frontend
npm run dev                          # Vite development server with HMR
npm run build                        # Production build

# Database
php artisan migrate                  # Run pending migrations
php artisan migrate:fresh --seed     # Fresh database with sample data
php artisan db:seed                  # Run only seeders
```

#### Code Quality & Testing
```bash
vendor/bin/pint                      # Code formatting (Laravel Pint)
composer test                        # Run tests with configuration cleanup
php artisan test                     # Run PHPUnit tests directly
php artisan test --filter=UserTest   # Run specific test
```

#### Admin Setup
```bash
php artisan create:super-admin        # Create super admin user
```

#### Module Generation
```bash
# Create new module (panel only)
php artisan make:module ModuleName

# Create module with web routes
php artisan make:module ModuleName --web

# Force overwrite existing module
php artisan make:module ModuleName --force
```

## 🏢 Modular Architecture

### Module Structure
Each module follows a consistent structure for maintainability and scalability:

```
app/Modules/{ModuleName}/
├── Controllers/                    # HTTP request handlers
│   ├── Panel/                     # Admin panel controllers
│   └── Web/                       # Frontend controllers
├── Services/                      # Business logic layer
├── Repositories/                  # Data access layer
├── Models/                        # Eloquent models
├── DTOs/                         # Data Transfer Objects
├── Actions/                      # Single-purpose operations
├── Requests/                     # Form validation rules
├── Exceptions/                   # Custom exception classes
├── Middleware/                   # Module-specific middleware
├── Helpers/                      # Utility functions
├── Panel/routes.php              # Admin routes
├── Web/routes.php                # Frontend routes
└── {ModuleName}ServiceProvider.php # Module service provider
```

### Core Modules

#### 1. User Module (`app/Modules/User/`)
- **Purpose**: Complete user management system
- **Features**: CRUD operations, role assignment, profile management
- **Models**: User, Role, Permission
- **Key Services**: UserService, RoleService, PermissionService
- **Authentication**: Laravel Jetstream with 2FA support

#### 2. ActivityLog Module (`app/Modules/ActivityLog/`)
- **Purpose**: System audit trail and user activity tracking
- **Features**: Automatic logging, filtering, bulk operations
- **Integration**: Spatie Laravel ActivityLog
- **Storage**: Comprehensive activity records with context

#### 3. MailNotification Module (`app/Modules/MailNotification/`)
- **Purpose**: Email management and logging system
- **Features**: Send emails, track delivery, retry failed emails
- **Logging**: Complete mail history with status tracking
- **Testing**: Built-in test email functionality

#### 4. Settings Module (`app/Modules/Settings/`)
- **Purpose**: Application configuration management
- **Features**: App settings, mail configuration, dynamic settings
- **Storage**: Database-driven configuration with caching

#### 5. Dashboard Module (`app/Modules/Dashboard/`)
- **Purpose**: Analytics and overview interface
- **Features**: Statistics, charts, system overview
- **Real-time**: Live data updates and monitoring

## 🎨 Frontend Architecture

### Technology Stack
- **Vue 3** with Composition API
- **Inertia.js** for SPA-like experience
- **Tailwind CSS** for styling
- **Vite** for fast builds and HMR
- **TypeScript-ready** (configurable)

### Component Organization
```
resources/js/
├── Components/
│   ├── Panel/                    # Admin panel components
│   │   ├── Actions/             # Buttons, search, export
│   │   ├── Forms/               # Form components
│   │   ├── Navigation/          # Sidebar, breadcrumbs
│   │   ├── Page/                # Page headers, stats
│   │   └── Shared/              # Reusable components
│   ├── Shared/                  # Jetstream components
│   └── Web/                     # Frontend components
├── Layouts/
│   ├── AppLayout.vue            # Main application layout
│   ├── PanelLayout.vue          # Admin panel layout
│   └── GuestLayout.vue          # Authentication layout
├── Modules/                     # Module-specific components
│   └── {ModuleName}/
│       ├── Panel/Components/    # Admin components
│       ├── Web/Components/      # Frontend components
│       └── Shared/components/   # Module shared components
├── Composables/                 # Vue 3 composables
├── Pages/                       # Inertia page components
└── Utils/                       # JavaScript utilities
```

### Key Composables
```javascript
// State Management
useForm()           // Form handling with validation
useModal()          // Modal state management
useLoading()        // Loading state control
useToggle()         // Toggle functionality

// Data Operations
usePagination()     // Pagination logic
useSearch()         // Search functionality
useExport()         // Data export operations
useDeleteModal()    // Delete confirmation workflow

// UI/UX
useNotification()   // Toast notifications
useNavigation()     // Routing and navigation
useFormat()         // Data formatting utilities
```

## 🔐 Security & Authentication

### Authentication System
- **Laravel Jetstream**: Modern authentication scaffolding
- **Two-Factor Authentication (2FA)**: Google2FA integration
- **Laravel Sanctum**: API token authentication
- **Email Verification**: Built-in email confirmation
- **Password Reset**: Secure password recovery

### Authorization & Permissions
- **Spatie Laravel Permission**: Role and permission management
- **Resource-Based Permissions**: Granular access control
- **Middleware Protection**: Route-level security
- **Dynamic Permission Checking**: Runtime permission validation

### Security Middleware
```php
UserSecurityMiddleware::class      // User-specific security checks
'auth:sanctum'                     // API authentication
'verified'                         // Email verification requirement
'permission:permission.name'       // Permission-based access
'role:role.name'                   // Role-based access
```

## 🗄️ Database Architecture

### Connection Configuration
- **Primary**: SQLite (development), MySQL/PostgreSQL (production)
- **Migrations**: Version-controlled schema changes
- **Seeders**: Sample data and role/permission setup
- **Factories**: Test data generation

### Key Tables
```sql
users                    # User accounts and profiles
roles                    # User roles (admin, user, etc.)
permissions              # System permissions
model_has_permissions    # User-specific permissions
model_has_roles         # User role assignments
activity_log            # System activity tracking
mail_logs              # Email delivery tracking
settings               # Application configuration
```

### Migration Strategy
```bash
# Database setup
php artisan migrate              # Apply pending migrations
php artisan migrate:rollback     # Rollback last batch
php artisan migrate:reset        # Rollback all migrations
php artisan migrate:refresh      # Reset and re-run all migrations
php artisan migrate:fresh --seed # Fresh database with seeders
```

## 🏛️ Service Layer Patterns

### Base Classes & Traits

#### BaseController (`app/Abstracts/BaseController.php`)
```php
// Consistent response methods
successResponse($data, $message)
errorResponse($message, $status)
jsonSuccessResponse($data, $message)
handleException($exception)
handleCustomException($exception)
```

#### BaseService with TransactionTrait
```php
// Database transaction methods
executeInTransaction($callback)
updateInTransaction($model, $data)
deleteInTransaction($model)
```

#### BaseRepository (`app/Abstracts/BaseRepository.php`)
```php
// Standard repository methods
getAll()
getAllPaginated($perPage)
findById($id)
findByIdOrFail($id)
create($data)
update($model, $data)
delete($model)
```

### Service Implementation Example
```php
class UserService
{
    use TransactionTrait;

    public function __construct(
        private UserRepository $userRepository,
        private RoleRepository $roleRepository
    ) {}

    public function create(UserDTO $userData): User
    {
        return $this->executeInTransaction(function () use ($userData) {
            $user = $this->userRepository->create($userData->toArray());
            
            if ($userData->role_id) {
                $role = $this->roleRepository->findByIdOrFail($userData->role_id);
                $user->assignRole($role);
            }

            activity('user')
                ->performedOn($user)
                ->log('User created');

            return $user;
        });
    }
}
```

## 🎯 Development Patterns & Best Practices

### Code Standards
- **PSR-12**: PHP coding standards
- **Laravel Conventions**: Framework-specific patterns
- **Vue 3 Best Practices**: Composition API, component naming
- **Type Hints**: PHP 8.2+ features with strict typing
- **DocBlocks**: Comprehensive code documentation

### Component Naming Conventions
```javascript
// Vue Components (PascalCase)
UserList.vue              // List components
UserListRow.vue           // Row/item components
UserCard.vue              // Card/display components
UserForm.vue              // Form components
UserModal.vue             // Modal components

// Composables (camelCase with 'use' prefix)
useUserManagement.js      // Module-specific logic
useFormValidation.js      // Reusable functionality
```

### Error Handling Strategy
```php
// Custom Exceptions
UserException::userNotFound($id)
UserException::invalidPermissions($user)
UserException::emailAlreadyExists($email)

// Global Exception Handler
app/Exceptions/Handler.php  // Centralized error handling
```

### Import Patterns
```javascript
// Relative imports for module components
import UserCard from './Components/UserCard.vue'

// Absolute imports for shared components
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

// Composables
import { useForm } from '@/Composables/useForm'
```

## 📊 Monitoring & Logging

### Activity Logging
```php
// Automatic logging with LogsActivity trait
activity('module')
    ->performedOn($model)
    ->withProperties(['key' => 'value'])
    ->log('Action description');

// Manual logging
activity()
    ->causedBy($user)
    ->performedOn($model)
    ->withProperties($data)
    ->log('Custom action');
```

### Mail Logging
All emails are automatically logged with:
- Delivery status tracking
- Retry mechanisms for failed emails
- Complete email history
- Performance monitoring

### System Monitoring
```bash
php artisan pail --timeout=0     # Real-time log monitoring
php artisan queue:work           # Queue processing
php artisan horizon             # Queue dashboard (if installed)
```

## 🧪 Testing Strategy

### Test Organization
```
tests/
├── Feature/                    # Integration tests
│   ├── Auth/                  # Authentication tests
│   ├── User/                  # User management tests
│   └── Api/                   # API endpoint tests
├── Unit/                      # Unit tests
│   ├── Services/              # Service layer tests
│   ├── Repositories/          # Repository tests
│   └── Helpers/               # Helper function tests
└── Browser/                   # Laravel Dusk tests (if configured)
```

### Testing Commands
```bash
php artisan test                    # Run all tests
php artisan test --filter=UserTest # Run specific test
php artisan test --coverage        # Generate coverage report
php artisan test --parallel        # Run tests in parallel
```

### Test Data Management
```php
// Factories for test data
UserFactory::new()->create()
UserFactory::new()->withRole('admin')->create()

// Database seeding for tests
$this->seed(RolePermissionSeeder::class)
```

## 🔧 Configuration & Environment

### Environment Files
```bash
.env                    # Main environment configuration
.env.example           # Template for environment setup
.env.testing           # Testing environment overrides
```

### Key Configuration Areas
```env
# Application
APP_NAME="Laravel Starter"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Queue Configuration
QUEUE_CONNECTION=database

# Authentication
SESSION_LIFETIME=120
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## 📦 Package Management

### Core Dependencies
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "laravel/jetstream": "^5.3",
  "laravel/sanctum": "^4.0",
  "inertiajs/inertia-laravel": "^2.0",
  "spatie/laravel-activitylog": "^4.10",
  "spatie/laravel-permission": "^6.20",
  "tightenco/ziggy": "^2.0"
}
```

### Frontend Dependencies
```json
{
  "@inertiajs/vue3": "^2.0",
  "vue": "^3.3.13",
  "@vitejs/plugin-vue": "^5.0.0",
  "tailwindcss": "^3.4.0",
  "@heroicons/vue": "^2.2.0"
}
```

## 🚀 Deployment Guidelines

### Production Environment Setup
```bash
# Dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database
php artisan migrate --force

# Storage
php artisan storage:link
```

### Performance Optimization
```bash
# Laravel optimizations
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Composer optimizations
composer install --optimize-autoloader --no-dev

# Frontend optimizations
npm run build
```

### Security Considerations
```env
# Production security settings
APP_DEBUG=false
APP_ENV=production
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

## 🎛️ Customization Guidelines

### Adding New Modules

#### Automated Module Generation
Use the built-in command to generate a complete module structure:

```bash
# Generate module with panel routes only
php artisan make:module YourModuleName

# Generate module with both panel and web routes
php artisan make:module YourModuleName --web

# Force overwrite if module exists
php artisan make:module YourModuleName --force
```

The command automatically creates:
- **Complete directory structure** following project standards
- **All base classes** (Service, Repository, Controller, Model, etc.)
- **Request validation classes** for create/update operations
- **Action classes** for specific operations
- **Exception classes** with common error scenarios
- **DTO class** for data transfer
- **Routes** (Panel and optionally Web)
- **ServiceProvider** registration in `bootstrap/providers.php`

#### Manual Module Creation (if needed)
1. **Create module directory** in `app/Modules/`
2. **Follow standard structure** (Controllers, Services, Repositories, etc.)
3. **Create ServiceProvider** and register in `bootstrap/providers.php`
4. **Add routes** (Panel/routes.php, Web/routes.php)
5. **Create frontend components** in `resources/js/Modules/`

#### Post-Generation Steps
After generating a module, you should:
1. **Create database migration** if needed: `php artisan make:migration create_[table_name]_table`
2. **Update model relationships** and fillable attributes
3. **Create frontend Vue components** in `resources/js/Modules/YourModule/Panel/`
4. **Add permissions** to the role/permission seeder
5. **Write tests** for the new functionality

### Extending Base Classes
```php
// Service extension
class CustomService extends BaseService
{
    use TransactionTrait;
    // Custom implementation
}

// Repository extension
class CustomRepository extends BaseRepository
{
    protected $model = CustomModel::class;
    // Custom methods
}
```

### Component Development
```javascript
// Reusable component pattern
<template>
  <div class="component-wrapper">
    <!-- Component content -->
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props with validation
const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  }
})

// Emits definition
const emit = defineEmits(['update', 'delete'])

// Reactive state
const loading = ref(false)

// Computed properties
const formattedData = computed(() => {
  // Processing logic
})
</script>
```

## 💡 Troubleshooting & Common Issues

### Development Issues
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Permission issues
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Node modules issues
rm -rf node_modules package-lock.json
npm install
```

### Database Issues
```bash
# Reset database
php artisan migrate:fresh --seed

# Check database connection
php artisan tinker
DB::connection()->getPdo()

# Fix migration issues
php artisan migrate:rollback
php artisan migrate
```

### Frontend Issues
```bash
# Vite issues
rm -rf node_modules
npm install
npm run dev

# Build issues
npm run build

# Port conflicts
npm run dev -- --port 3001
```

## 📝 Important Notes for Claude Code

### When Working with This Project:

1. **Always Use Base Classes**: Extend BaseController, BaseService, BaseRepository
2. **Follow Module Structure**: Keep module-specific code in respective directories
3. **Use Transactions**: Wrap database operations in transactions via TransactionTrait
4. **Consistent Error Handling**: Use BaseException and consistent error responses
5. **Frontend Patterns**: Use Composition API, proper component naming, relative imports
6. **Security First**: Always consider permissions and authentication
7. **Activity Logging**: Log important user actions for audit trails
8. **Code Quality**: Run Pint before committing, write tests for new features

### File Modification Priorities:
1. **Service Layer**: Business logic goes in Services
2. **Repository Layer**: Data access in Repositories
3. **Controller Layer**: HTTP handling only, delegate to Services
4. **Frontend Components**: Follow Vue 3 best practices
5. **Routes**: Keep organized in module-specific route files

### Testing Requirements:
- Write tests for new Services and Repositories
- Test permission checking in Controllers
- Verify frontend components work correctly
- Ensure database transactions work properly

This documentation should provide comprehensive guidance for working effectively with this Laravel starter project.