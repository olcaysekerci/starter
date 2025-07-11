<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CreateModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name} {--web : Include web routes and controller} {--force : Force overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module with all necessary components';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $includeWeb = $this->option('web');
        $force = $this->option('force');

        // Validate module name
        if (!$this->validateModuleName($name)) {
            $this->error('Invalid module name. Please use PascalCase format (e.g., UserManagement)');
            return 1;
        }

        $studlyName = Str::studly($name);
        $modulePath = app_path("Modules/{$studlyName}");

        // Check if module already exists
        if ($this->files->exists($modulePath) && !$force) {
            $this->error("Module {$studlyName} already exists! Use --force to overwrite.");
            return 1;
        }

        $this->info("Creating module: {$studlyName}");

        try {
            // Create module structure
            $this->createModuleStructure($studlyName, $modulePath, $includeWeb);
            
            // Register service provider
            $this->registerServiceProvider($studlyName);
            
            $this->info("✅ Module {$studlyName} created successfully!");
            $this->info("📝 Don't forget to:");
            $this->info("   - Add migrations if needed");
            $this->info("   - Update frontend routes and components");
            $this->info("   - Configure permissions and policies");
            
        } catch (\Exception $e) {
            $this->error("Failed to create module: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function validateModuleName(string $name): bool
    {
        // Check if name is in PascalCase format
        return preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name);
    }

    protected function createModuleStructure(string $studlyName, string $modulePath, bool $includeWeb): void
    {
        $snakeName = Str::snake($studlyName);
        $kebabName = Str::kebab($studlyName);
        $camelName = Str::camel($studlyName);
        $pluralName = Str::plural($studlyName);
        $lowerName = Str::lower($studlyName);

        // Create directory structure
        $directories = [
            'Actions',
            'Controllers/Panel',
            'DTOs',
            'Exceptions',
            'Models',
            'Panel',
            'Repositories',
            'Requests',
            'Services',
        ];

        if ($includeWeb) {
            $directories[] = 'Web/Controllers';
            $directories[] = 'Web';
        }

        foreach ($directories as $dir) {
            $this->files->makeDirectory("{$modulePath}/{$dir}", 0755, true, true);
        }

        // Generate files
        $this->generateServiceProvider($modulePath, $studlyName, $includeWeb);
        $this->generateModel($modulePath, $studlyName);
        $this->generateDTO($modulePath, $studlyName);
        $this->generateException($modulePath, $studlyName);
        $this->generateRepository($modulePath, $studlyName);
        $this->generateService($modulePath, $studlyName);
        $this->generatePanelController($modulePath, $studlyName);
        $this->generatePanelRoutes($modulePath, $studlyName, $kebabName);
        $this->generateCreateRequest($modulePath, $studlyName);
        $this->generateUpdateRequest($modulePath, $studlyName);
        $this->generateCreateAction($modulePath, $studlyName);
        $this->generateUpdateAction($modulePath, $studlyName);

        if ($includeWeb) {
            $this->generateWebController($modulePath, $studlyName);
            $this->generateWebRoutes($modulePath, $studlyName, $kebabName);
        }

        $this->info("📁 Created module structure for {$studlyName}");
    }

    protected function generateServiceProvider(string $modulePath, string $studlyName, bool $includeWeb): void
    {
        $template = $this->getServiceProviderTemplate($studlyName, $includeWeb);
        $this->files->put("{$modulePath}/{$studlyName}ServiceProvider.php", $template);
    }

    protected function generateModel(string $modulePath, string $studlyName): void
    {
        $template = $this->getModelTemplate($studlyName);
        $this->files->put("{$modulePath}/Models/{$studlyName}.php", $template);
    }

    protected function generateDTO(string $modulePath, string $studlyName): void
    {
        $template = $this->getDTOTemplate($studlyName);
        $this->files->put("{$modulePath}/DTOs/{$studlyName}DTO.php", $template);
    }

    protected function generateException(string $modulePath, string $studlyName): void
    {
        $template = $this->getExceptionTemplate($studlyName);
        $this->files->put("{$modulePath}/Exceptions/{$studlyName}Exception.php", $template);
    }

    protected function generateRepository(string $modulePath, string $studlyName): void
    {
        $template = $this->getRepositoryTemplate($studlyName);
        $this->files->put("{$modulePath}/Repositories/{$studlyName}Repository.php", $template);
    }

    protected function generateService(string $modulePath, string $studlyName): void
    {
        $template = $this->getServiceTemplate($studlyName);
        $this->files->put("{$modulePath}/Services/{$studlyName}Service.php", $template);
    }

    protected function generatePanelController(string $modulePath, string $studlyName): void
    {
        $template = $this->getPanelControllerTemplate($studlyName);
        $this->files->put("{$modulePath}/Controllers/Panel/{$studlyName}Controller.php", $template);
    }

    protected function generateWebController(string $modulePath, string $studlyName): void
    {
        $template = $this->getWebControllerTemplate($studlyName);
        $this->files->put("{$modulePath}/Web/Controllers/{$studlyName}Controller.php", $template);
    }

    protected function generatePanelRoutes(string $modulePath, string $studlyName, string $kebabName): void
    {
        $template = $this->getPanelRoutesTemplate($studlyName, $kebabName);
        $this->files->put("{$modulePath}/Panel/routes.php", $template);
    }

    protected function generateWebRoutes(string $modulePath, string $studlyName, string $kebabName): void
    {
        $template = $this->getWebRoutesTemplate($studlyName, $kebabName);
        $this->files->put("{$modulePath}/Web/routes.php", $template);
    }

    protected function generateCreateRequest(string $modulePath, string $studlyName): void
    {
        $template = $this->getCreateRequestTemplate($studlyName);
        $this->files->put("{$modulePath}/Requests/Create{$studlyName}Request.php", $template);
    }

    protected function generateUpdateRequest(string $modulePath, string $studlyName): void
    {
        $template = $this->getUpdateRequestTemplate($studlyName);
        $this->files->put("{$modulePath}/Requests/Update{$studlyName}Request.php", $template);
    }

    protected function generateCreateAction(string $modulePath, string $studlyName): void
    {
        $template = $this->getCreateActionTemplate($studlyName);
        $this->files->put("{$modulePath}/Actions/Create{$studlyName}Action.php", $template);
    }

    protected function generateUpdateAction(string $modulePath, string $studlyName): void
    {
        $template = $this->getUpdateActionTemplate($studlyName);
        $this->files->put("{$modulePath}/Actions/Update{$studlyName}Action.php", $template);
    }

    protected function registerServiceProvider(string $studlyName): void
    {
        $providersPath = base_path('bootstrap/providers.php');
        $providersContent = $this->files->get($providersPath);
        
        $serviceProvider = "    App\\Modules\\{$studlyName}\\{$studlyName}ServiceProvider::class,";
        
        // Check if already registered
        if (strpos($providersContent, $serviceProvider) !== false) {
            $this->info("Service provider already registered");
            return;
        }

        // Find the last service provider and add our service provider before the closing bracket
        $pattern = '/(\s+)(.*ServiceProvider::class,)(\s*\];)/s';
        
        if (preg_match($pattern, $providersContent, $matches)) {
            $replacement = $matches[1] . $matches[2] . "\n" . $serviceProvider . $matches[3];
            $updatedContent = preg_replace($pattern, $replacement, $providersContent);
            
            if ($updatedContent !== null && $updatedContent !== $providersContent) {
                $this->files->put($providersPath, $updatedContent);
                $this->info("✅ Registered service provider in bootstrap/providers.php");
            } else {
                $this->warn("⚠️  Could not automatically register service provider. Please add manually:");
                $this->warn($serviceProvider);
            }
        } else {
            $this->warn("⚠️  Could not automatically register service provider. Please add manually:");
            $this->warn($serviceProvider);
        }
    }

    // Template methods
    protected function getServiceProviderTemplate(string $studlyName, bool $includeWeb): string
    {
        $webRoutes = $includeWeb ? "\n        \$this->loadRoutesFrom(__DIR__ . '/Web/routes.php');" : '';
        
        return "<?php

namespace App\Modules\\{$studlyName};

use Illuminate\Support\ServiceProvider;

class {$studlyName}ServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // {$studlyName} modülü servislerini kaydet
        \$this->app->bind(\\App\\Modules\\{$studlyName}\\Services\\{$studlyName}Service::class);
        
        // {$studlyName} modülü repository'lerini kaydet
        \$this->app->bind(\\App\\Modules\\{$studlyName}\\Repositories\\{$studlyName}Repository::class);
        
        // {$studlyName} modülü action'larını kaydet
        \$this->app->bind(\\App\\Modules\\{$studlyName}\\Actions\\Create{$studlyName}Action::class);
        \$this->app->bind(\\App\\Modules\\{$studlyName}\\Actions\\Update{$studlyName}Action::class);
        
        // {$studlyName} modülü model'lerini kaydet
        \$this->app->bind(\\App\\Modules\\{$studlyName}\\Models\\{$studlyName}::class, function (\$app) {
            return new \\App\\Modules\\{$studlyName}\\Models\\{$studlyName}();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // {$studlyName} modülü route'larını yükle
        \$this->loadRoutesFrom(__DIR__ . '/Panel/routes.php');{$webRoutes}
        
        // {$studlyName} modülü view'larını yükle (eğer blade view'ları kullanılacaksa)
        // \$this->loadViewsFrom(__DIR__ . '/Views', '" . Str::lower($studlyName) . "');
        
        // {$studlyName} modülü migration'larını yükle
        // \$this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        
        // {$studlyName} modülü config dosyasını yükle
        // \$this->publishes([
        //     __DIR__ . '/Config/" . Str::lower($studlyName) . ".php' => config_path('" . Str::lower($studlyName) . ".php'),
        // ], '" . Str::lower($studlyName) . "-config');
    }
}
";
    }

    protected function getModelTemplate(string $studlyName): string
    {
        $tableName = Str::snake(Str::plural($studlyName));
        
        return "<?php

namespace App\Modules\\{$studlyName}\\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\\{$studlyName}\\Traits\\LogsActivity;

class {$studlyName} extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected \$table = '{$tableName}';

    protected \$fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected \$casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected \$attributes = [
        'is_active' => true,
    ];

    /**
     * Activity log için model adı
     */
    protected static \$logName = '" . Str::lower($studlyName) . "';

    /**
     * Activity log için kayıt edilecek attribute'lar
     */
    protected static \$logAttributes = [
        'name',
        'description', 
        'is_active',
    ];

    /**
     * Activity log için sadece değişen attribute'ları kaydet
     */
    protected static \$logOnlyDirty = true;

    /**
     * Activity log mesajlarını özelleştir
     */
    public function getDescriptionForEvent(string \$eventName): string
    {
        return \"{$studlyName} {\$eventName}\";
    }

    // Scopes
    public function scopeActive(\$query)
    {
        return \$query->where('is_active', true);
    }

    public function scopeInactive(\$query)
    {
        return \$query->where('is_active', false);
    }

    // Accessors & Mutators
    public function getNameAttribute(\$value): string
    {
        return ucfirst(\$value);
    }

    // Relations
    // Add your model relationships here
}
";
    }

    protected function getDTOTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\DTOs;

class {$studlyName}DTO
{
    public function __construct(
        public readonly ?int \$id = null,
        public readonly ?string \$name = null,
        public readonly ?string \$description = null,
        public readonly bool \$is_active = true,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array \$data): self
    {
        return new self(
            id: \$data['id'] ?? null,
            name: \$data['name'] ?? null,
            description: \$data['description'] ?? null,
            is_active: \$data['is_active'] ?? true,
        );
    }

    /**
     * Create DTO from request
     */
    public static function fromRequest(\$request): self
    {
        return new self(
            id: \$request->input('id'),
            name: \$request->input('name'),
            description: \$request->input('description'),
            is_active: \$request->boolean('is_active', true),
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => \$this->id,
            'name' => \$this->name,
            'description' => \$this->description,
            'is_active' => \$this->is_active,
        ], fn(\$value) => \$value !== null);
    }

    /**
     * Convert to array for create operations (excluding id)
     */
    public function toCreateArray(): array
    {
        \$data = \$this->toArray();
        unset(\$data['id']);
        return \$data;
    }

    /**
     * Convert to array for update operations (excluding null values)
     */
    public function toUpdateArray(): array
    {
        \$data = \$this->toArray();
        unset(\$data['id']);
        return array_filter(\$data, fn(\$value) => \$value !== null);
    }
}
";
    }

    protected function getExceptionTemplate(string $studlyName): string
    {
        $lowerName = Str::lower($studlyName);
        
        return "<?php

namespace App\Modules\\{$studlyName}\\Exceptions;

use App\Abstracts\\BaseException;

class {$studlyName}Exception extends BaseException
{
    /**
     * {$studlyName} not found exception
     */
    public static function {$lowerName}NotFound(int \$id): self
    {
        return new self(
            message: \"{$studlyName} not found with ID: {\$id}\",
            code: 404,
            context: ['id' => \$id]
        );
    }

    /**
     * {$studlyName} creation failed exception
     */
    public static function {$lowerName}CreationFailed(array \$data = []): self
    {
        return new self(
            message: \"{$studlyName} could not be created\",
            code: 500,
            context: ['data' => \$data]
        );
    }

    /**
     * {$studlyName} update failed exception
     */
    public static function {$lowerName}UpdateFailed(int \$id, array \$data = []): self
    {
        return new self(
            message: \"{$studlyName} could not be updated\",
            code: 500,
            context: ['id' => \$id, 'data' => \$data]
        );
    }

    /**
     * {$studlyName} deletion failed exception
     */
    public static function {$lowerName}DeletionFailed(int \$id): self
    {
        return new self(
            message: \"{$studlyName} could not be deleted\",
            code: 500,
            context: ['id' => \$id]
        );
    }

    /**
     * {$studlyName} already exists exception
     */
    public static function {$lowerName}AlreadyExists(string \$name): self
    {
        return new self(
            message: \"{$studlyName} with name '{\$name}' already exists\",
            code: 409,
            context: ['name' => \$name]
        );
    }

    /**
     * Invalid {$studlyName} data exception
     */
    public static function invalid{$studlyName}Data(array \$errors = []): self
    {
        return new self(
            message: \"Invalid {$studlyName} data provided\",
            code: 422,
            context: ['errors' => \$errors]
        );
    }

    /**
     * {$studlyName} permission denied exception
     */
    public static function {$lowerName}PermissionDenied(): self
    {
        return new self(
            message: \"You don't have permission to perform this action on {$studlyName}\",
            code: 403
        );
    }
}
";
    }

    protected function getRepositoryTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\Repositories;

use App\Abstracts\\BaseRepository;
use App\Contracts\\BaseRepositoryInterface;
use App\Modules\\{$studlyName}\\Models\\{$studlyName};
use Illuminate\Database\\Eloquent\\Collection;
use Illuminate\\Pagination\\LengthAwarePaginator;

class {$studlyName}Repository extends BaseRepository implements BaseRepositoryInterface
{
    public function __construct(
        private {$studlyName} \$model
    ) {
        parent::__construct(\$model);
    }

    /**
     * Get all {$studlyName} records
     */
    public function getAll(): Collection
    {
        return \$this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated {$studlyName} records
     */
    public function getAllPaginated(int \$perPage = 15): LengthAwarePaginator
    {
        return \$this->model
            ->orderBy('created_at', 'desc')
            ->paginate(\$perPage);
    }

    /**
     * Find {$studlyName} by ID
     */
    public function findById(int \$id): ?{$studlyName}
    {
        return \$this->model->find(\$id);
    }

    /**
     * Find {$studlyName} by ID or fail
     */
    public function findByIdOrFail(int \$id): {$studlyName}
    {
        return \$this->model->findOrFail(\$id);
    }

    /**
     * Create new {$studlyName}
     */
    public function create(array \$data): {$studlyName}
    {
        return \$this->model->create(\$data);
    }

    /**
     * Update {$studlyName}
     */
    public function update(\$model, array \$data): bool
    {
        return \$model->update(\$data);
    }

    /**
     * Delete {$studlyName}
     */
    public function delete(\$model): bool
    {
        return \$model->delete();
    }

    /**
     * Get active {$studlyName} records
     */
    public function getActive(): Collection
    {
        return \$this->model->active()->orderBy('name')->get();
    }

    /**
     * Get inactive {$studlyName} records
     */
    public function getInactive(): Collection
    {
        return \$this->model->inactive()->orderBy('name')->get();
    }

    /**
     * Find {$studlyName} by name
     */
    public function findByName(string \$name): ?{$studlyName}
    {
        return \$this->model->where('name', \$name)->first();
    }

    /**
     * Search {$studlyName} records
     */
    public function search(string \$query, int \$perPage = 15): LengthAwarePaginator
    {
        return \$this->model
            ->where('name', 'like', \"%{\$query}%\")
            ->orWhere('description', 'like', \"%{\$query}%\")
            ->orderBy('created_at', 'desc')
            ->paginate(\$perPage);
    }

    /**
     * Get {$studlyName} statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => \$this->model->count(),
            'active' => \$this->model->active()->count(),
            'inactive' => \$this->model->inactive()->count(),
            'recent' => \$this->model->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
}
";
    }

    protected function getServiceTemplate(string $studlyName): string
    {
        $lowerName = Str::lower($studlyName);
        
        return "<?php

namespace App\Modules\\{$studlyName}\\Services;

use App\Traits\\TransactionTrait;
use App\Modules\\{$studlyName}\\Models\\{$studlyName};
use App\Modules\\{$studlyName}\\Repositories\\{$studlyName}Repository;
use App\Modules\\{$studlyName}\\DTOs\\{$studlyName}DTO;
use App\Modules\\{$studlyName}\\Exceptions\\{$studlyName}Exception;
use Illuminate\\Database\\Eloquent\\Collection;
use Illuminate\\Pagination\\LengthAwarePaginator;

class {$studlyName}Service
{
    use TransactionTrait;

    public function __construct(
        private {$studlyName}Repository \${$lowerName}Repository
    ) {}

    /**
     * Get all {$studlyName} records
     */
    public function getAll(): Collection
    {
        return \$this->{$lowerName}Repository->getAll();
    }

    /**
     * Get paginated {$studlyName} records
     */
    public function getAllPaginated(int \$perPage = 15): LengthAwarePaginator
    {
        return \$this->{$lowerName}Repository->getAllPaginated(\$perPage);
    }

    /**
     * Find {$studlyName} by ID
     */
    public function findById(int \$id): ?{$studlyName}
    {
        return \$this->{$lowerName}Repository->findById(\$id);
    }

    /**
     * Find {$studlyName} by ID or fail
     */
    public function findByIdOrFail(int \$id): {$studlyName}
    {
        \${$lowerName} = \$this->{$lowerName}Repository->findById(\$id);
        
        if (!\${$lowerName}) {
            throw {$studlyName}Exception::{$lowerName}NotFound(\$id);
        }
        
        return \${$lowerName};
    }

    /**
     * Create new {$studlyName}
     */
    public function create({$studlyName}DTO \$data): {$studlyName}
    {
        return \$this->executeInTransaction(function () use (\$data) {
            // Check if {$studlyName} with same name already exists
            if (\$data->name && \$this->{$lowerName}Repository->findByName(\$data->name)) {
                throw {$studlyName}Exception::{$lowerName}AlreadyExists(\$data->name);
            }

            \${$lowerName} = \$this->{$lowerName}Repository->create(\$data->toCreateArray());

            if (!\${$lowerName}) {
                throw {$studlyName}Exception::{$lowerName}CreationFailed(\$data->toArray());
            }

            // Activity log
            activity('" . Str::lower($studlyName) . "')
                ->causedBy(auth()->user())
                ->performedOn(\${$lowerName})
                ->withProperties([
                    'attributes' => \${$lowerName}->getAttributes(),
                ])
                ->log('{$studlyName} created');

            return \${$lowerName};
        });
    }

    /**
     * Update {$studlyName}
     */
    public function update(int \$id, {$studlyName}DTO \$data): {$studlyName}
    {
        return \$this->updateInTransaction(function () use (\$id, \$data) {
            \${$lowerName} = \$this->findByIdOrFail(\$id);

            // Check if {$studlyName} with same name already exists (excluding current)
            if (\$data->name && \$data->name !== \${$lowerName}->name) {
                \$existing = \$this->{$lowerName}Repository->findByName(\$data->name);
                if (\$existing && \$existing->id !== \$id) {
                    throw {$studlyName}Exception::{$lowerName}AlreadyExists(\$data->name);
                }
            }

            \$oldAttributes = \${$lowerName}->getAttributes();
            \$success = \$this->{$lowerName}Repository->update(\${$lowerName}, \$data->toUpdateArray());

            if (!\$success) {
                throw {$studlyName}Exception::{$lowerName}UpdateFailed(\$id, \$data->toArray());
            }

            \${$lowerName}->refresh();

            // Activity log
            activity('" . Str::lower($studlyName) . "')
                ->causedBy(auth()->user())
                ->performedOn(\${$lowerName})
                ->withProperties([
                    'old' => \$oldAttributes,
                    'attributes' => \${$lowerName}->getAttributes(),
                ])
                ->log('{$studlyName} updated');

            return \${$lowerName};
        }, '{$lowerName} update');
    }

    /**
     * Delete {$studlyName}
     */
    public function delete(int \$id): bool
    {
        return \$this->deleteInTransaction(function () use (\$id) {
            \${$lowerName} = \$this->findByIdOrFail(\$id);
            
            \$success = \$this->{$lowerName}Repository->delete(\${$lowerName});

            if (!\$success) {
                throw {$studlyName}Exception::{$lowerName}DeletionFailed(\$id);
            }

            // Activity log
            activity('" . Str::lower($studlyName) . "')
                ->causedBy(auth()->user())
                ->withProperties([
                    'attributes' => \${$lowerName}->getAttributes(),
                ])
                ->log('{$studlyName} deleted');

            return \$success;
        }, '{$lowerName} deletion');
    }

    /**
     * Get active {$studlyName} records
     */
    public function getActive(): Collection
    {
        return \$this->{$lowerName}Repository->getActive();
    }

    /**
     * Search {$studlyName} records
     */
    public function search(string \$query, int \$perPage = 15): LengthAwarePaginator
    {
        return \$this->{$lowerName}Repository->search(\$query, \$perPage);
    }

    /**
     * Get {$studlyName} statistics
     */
    public function getStatistics(): array
    {
        return \$this->{$lowerName}Repository->getStatistics();
    }

    /**
     * Toggle {$studlyName} status
     */
    public function toggleStatus(int \$id): {$studlyName}
    {
        \${$lowerName} = \$this->findByIdOrFail(\$id);
        
        \$data = {$studlyName}DTO::fromArray(['is_active' => !\${$lowerName}->is_active]);
        
        return \$this->update(\$id, \$data);
    }
}
";
    }

    protected function getPanelControllerTemplate(string $studlyName): string
    {
        $lowerName = Str::lower($studlyName);
        $kebabName = Str::kebab($studlyName);
        $pluralKebab = Str::kebab(Str::plural($studlyName));
        
        return "<?php

namespace App\Modules\\{$studlyName}\\Controllers\\Panel;

use App\Abstracts\\BaseController;
use App\Modules\\{$studlyName}\\Services\\{$studlyName}Service;
use App\Modules\\{$studlyName}\\DTOs\\{$studlyName}DTO;
use App\Modules\\{$studlyName}\\Requests\\Create{$studlyName}Request;
use App\Modules\\{$studlyName}\\Requests\\Update{$studlyName}Request;
use App\Modules\\{$studlyName}\\Exceptions\\{$studlyName}Exception;
use Illuminate\\Http\\Request;
use Inertia\\Inertia;
use Inertia\\Response;
use Illuminate\\Http\\RedirectResponse;

class {$studlyName}Controller extends BaseController
{
    public function __construct(
        private {$studlyName}Service \${$lowerName}Service
    ) {}

    /**
     * Display a listing of {$studlyName} records
     */
    public function index(Request \$request): Response
    {
        try {
            \$perPage = \$request->get('per_page', 15);
            \$search = \$request->get('search');

            if (\$search) {
                \${$pluralKebab} = \$this->{$lowerName}Service->search(\$search, \$perPage);
            } else {
                \${$pluralKebab} = \$this->{$lowerName}Service->getAllPaginated(\$perPage);
            }

            \$statistics = \$this->{$lowerName}Service->getStatistics();

            return Inertia::render('{$studlyName}/Panel/Index', [
                '{$pluralKebab}' => \${$pluralKebab},
                'statistics' => \$statistics,
                'filters' => [
                    'search' => \$search,
                    'per_page' => \$perPage,
                ],
            ]);
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error loading {$pluralKebab}');
        }
    }

    /**
     * Show the form for creating a new {$studlyName}
     */
    public function create(): Response
    {
        return Inertia::render('{$studlyName}/Panel/Create');
    }

    /**
     * Store a newly created {$studlyName}
     */
    public function store(Create{$studlyName}Request \$request): RedirectResponse
    {
        try {
            \$data = {$studlyName}DTO::fromRequest(\$request);
            \${$lowerName} = \$this->{$lowerName}Service->create(\$data);

            return redirect()
                ->route('panel.{$pluralKebab}.show', \${$lowerName}->id)
                ->with('flash.success', '{$studlyName} successfully created');

        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error creating {$lowerName}');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error creating {$lowerName}');
        }
    }

    /**
     * Display the specified {$studlyName}
     */
    public function show(int \$id): Response|RedirectResponse
    {
        try {
            \${$lowerName} = \$this->{$lowerName}Service->findByIdOrFail(\$id);

            return Inertia::render('{$studlyName}/Panel/Show', [
                '{$kebabName}' => \${$lowerName},
            ]);
        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error loading {$lowerName}');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error loading {$lowerName}');
        }
    }

    /**
     * Show the form for editing the specified {$studlyName}
     */
    public function edit(int \$id): Response|RedirectResponse
    {
        try {
            \${$lowerName} = \$this->{$lowerName}Service->findByIdOrFail(\$id);

            return Inertia::render('{$studlyName}/Panel/Edit', [
                '{$kebabName}' => \${$lowerName},
            ]);
        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error loading {$lowerName} for editing');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error loading {$lowerName} for editing');
        }
    }

    /**
     * Update the specified {$studlyName}
     */
    public function update(Update{$studlyName}Request \$request, int \$id): RedirectResponse
    {
        try {
            \$data = {$studlyName}DTO::fromRequest(\$request);
            \${$lowerName} = \$this->{$lowerName}Service->update(\$id, \$data);

            return redirect()
                ->route('panel.{$pluralKebab}.show', \${$lowerName}->id)
                ->with('flash.success', '{$studlyName} successfully updated');

        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error updating {$lowerName}');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error updating {$lowerName}');
        }
    }

    /**
     * Remove the specified {$studlyName}
     */
    public function destroy(int \$id): RedirectResponse
    {
        try {
            \$this->{$lowerName}Service->delete(\$id);

            return redirect()
                ->route('panel.{$pluralKebab}.index')
                ->with('flash.success', '{$studlyName} successfully deleted');

        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error deleting {$lowerName}');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error deleting {$lowerName}');
        }
    }

    /**
     * Toggle {$studlyName} status
     */
    public function toggleStatus(int \$id): RedirectResponse
    {
        try {
            \${$lowerName} = \$this->{$lowerName}Service->toggleStatus(\$id);
            
            \$status = \${$lowerName}->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->back()
                ->with('flash.success', \"{$studlyName} successfully {\$status}\");

        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error updating {$lowerName} status');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error updating {$lowerName} status');
        }
    }
}
";
    }

    protected function getWebControllerTemplate(string $studlyName): string
    {
        $lowerName = Str::lower($studlyName);
        $kebabName = Str::kebab($studlyName);
        $pluralKebab = Str::kebab(Str::plural($studlyName));
        
        return "<?php

namespace App\Modules\\{$studlyName}\\Web\\Controllers;

use App\Abstracts\\BaseController;
use App\Modules\\{$studlyName}\\Services\\{$studlyName}Service;
use App\Modules\\{$studlyName}\\Exceptions\\{$studlyName}Exception;
use Illuminate\\Http\\Request;
use Inertia\\Inertia;
use Inertia\\Response;

class {$studlyName}Controller extends BaseController
{
    public function __construct(
        private {$studlyName}Service \${$lowerName}Service
    ) {}

    /**
     * Display a listing of {$studlyName} records
     */
    public function index(Request \$request): Response
    {
        try {
            \$perPage = \$request->get('per_page', 12);
            \$search = \$request->get('search');

            if (\$search) {
                \${$pluralKebab} = \$this->{$lowerName}Service->search(\$search, \$perPage);
            } else {
                \${$pluralKebab} = \$this->{$lowerName}Service->getAllPaginated(\$perPage);
            }

            return Inertia::render('{$studlyName}/Web/Index', [
                '{$pluralKebab}' => \${$pluralKebab},
                'filters' => [
                    'search' => \$search,
                    'per_page' => \$perPage,
                ],
            ]);
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error loading {$pluralKebab}');
        }
    }

    /**
     * Display the specified {$studlyName}
     */
    public function show(int \$id): Response
    {
        try {
            \${$lowerName} = \$this->{$lowerName}Service->findByIdOrFail(\$id);

            // Only show active records to public
            if (!\${$lowerName}->is_active) {
                abort(404);
            }

            return Inertia::render('{$studlyName}/Web/Show', [
                '{$kebabName}' => \${$lowerName},
            ]);
        } catch ({$studlyName}Exception \$e) {
            return \$this->handleCustomException(\$e, 'Error loading {$lowerName}');
        } catch (\Exception \$e) {
            return \$this->handleException(\$e, 'Error loading {$lowerName}');
        }
    }
}
";
    }

    protected function getPanelRoutesTemplate(string $studlyName, string $kebabName): string
    {
        $pluralKebab = Str::kebab(Str::plural($studlyName));
        
        return "<?php

use Illuminate\\Support\\Facades\\Route;
use App\\Modules\\{$studlyName}\\Controllers\\Panel\\{$studlyName}Controller;

/*
|--------------------------------------------------------------------------
| {$studlyName} Panel Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the {$studlyName}ServiceProvider within a group
| which contains the \"web\" middleware group and panel prefix.
|
*/

Route::middleware(['web', 'auth:sanctum', 'verified'])
    ->prefix('panel')
    ->name('panel.')
    ->group(function () {
        
        // {$studlyName} CRUD routes
        Route::resource('{$pluralKebab}', {$studlyName}Controller::class)->parameters([
            '{$pluralKebab}' => 'id'
        ]);
        
        // Additional {$studlyName} routes
        Route::put('{$pluralKebab}/{id}/toggle-status', [{$studlyName}Controller::class, 'toggleStatus'])
            ->name('{$pluralKebab}.toggle-status');
            
        // Add more custom routes here as needed
        // Route::get('{$pluralKebab}/{id}/export', [{$studlyName}Controller::class, 'export'])
        //     ->name('{$pluralKebab}.export');
    });
";
    }

    protected function getWebRoutesTemplate(string $studlyName, string $kebabName): string
    {
        $pluralKebab = Str::kebab(Str::plural($studlyName));
        
        return "<?php

use Illuminate\\Support\\Facades\\Route;
use App\\Modules\\{$studlyName}\\Web\\Controllers\\{$studlyName}Controller;

/*
|--------------------------------------------------------------------------
| {$studlyName} Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the {$studlyName}ServiceProvider within a group
| which contains the \"web\" middleware group for public access.
|
*/

Route::middleware(['web'])
    ->group(function () {
        
        // Public {$studlyName} routes
        Route::get('{$pluralKebab}', [{$studlyName}Controller::class, 'index'])
            ->name('{$pluralKebab}.index');
            
        Route::get('{$pluralKebab}/{id}', [{$studlyName}Controller::class, 'show'])
            ->where('id', '[0-9]+')
            ->name('{$pluralKebab}.show');
    });
";
    }

    protected function getCreateRequestTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class Create{$studlyName}Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:" . Str::snake(Str::plural($studlyName)) . ",name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'is_active' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'A " . Str::lower($studlyName) . " with this name already exists.',
            'name.max' => 'The name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        \$this->merge([
            'is_active' => \$this->boolean('is_active', true),
        ]);
    }
}
";
    }

    protected function getUpdateRequestTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;

class Update{$studlyName}Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        \$id = \$this->route('id');
        
        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('" . Str::snake(Str::plural($studlyName)) . "', 'name')->ignore(\$id)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'is_active' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'A " . Str::lower($studlyName) . " with this name already exists.',
            'name.max' => 'The name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        \$this->merge([
            'is_active' => \$this->boolean('is_active'),
        ]);
    }
}
";
    }

    protected function getCreateActionTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\Actions;

use App\Modules\\{$studlyName}\\Models\\{$studlyName};
use App\Modules\\{$studlyName}\\DTOs\\{$studlyName}DTO;
use App\Modules\\{$studlyName}\\Services\\{$studlyName}Service;

class Create{$studlyName}Action
{
    public function __construct(
        private {$studlyName}Service \$service
    ) {}

    /**
     * Execute the action to create a new {$studlyName}
     */
    public function execute({$studlyName}DTO \$data): {$studlyName}
    {
        // Perform any additional business logic here
        // before creating the {$studlyName}
        
        // You can add custom validation, 
        // data transformation, or other operations
        
        return \$this->service->create(\$data);
    }

    /**
     * Execute with additional validation or processing
     */
    public function executeWithValidation(array \$data): {$studlyName}
    {
        // Custom validation logic
        \$this->validateCustomRules(\$data);
        
        // Transform data if needed
        \$transformedData = \$this->transformData(\$data);
        
        // Create DTO and execute
        \$dto = {$studlyName}DTO::fromArray(\$transformedData);
        
        return \$this->execute(\$dto);
    }

    /**
     * Custom validation rules specific to this action
     */
    private function validateCustomRules(array \$data): void
    {
        // Add custom validation logic here
        // Throw exceptions if validation fails
    }

    /**
     * Transform data before processing
     */
    private function transformData(array \$data): array
    {
        // Add any data transformation logic here
        return \$data;
    }
}
";
    }

    protected function getUpdateActionTemplate(string $studlyName): string
    {
        return "<?php

namespace App\Modules\\{$studlyName}\\Actions;

use App\Modules\\{$studlyName}\\Models\\{$studlyName};
use App\Modules\\{$studlyName}\\DTOs\\{$studlyName}DTO;
use App\Modules\\{$studlyName}\\Services\\{$studlyName}Service;

class Update{$studlyName}Action
{
    public function __construct(
        private {$studlyName}Service \$service
    ) {}

    /**
     * Execute the action to update a {$studlyName}
     */
    public function execute(int \$id, {$studlyName}DTO \$data): {$studlyName}
    {
        // Perform any additional business logic here
        // before updating the {$studlyName}
        
        // You can add custom validation, 
        // data transformation, or other operations
        
        return \$this->service->update(\$id, \$data);
    }

    /**
     * Execute with additional validation or processing
     */
    public function executeWithValidation(int \$id, array \$data): {$studlyName}
    {
        // Custom validation logic
        \$this->validateCustomRules(\$id, \$data);
        
        // Transform data if needed
        \$transformedData = \$this->transformData(\$data);
        
        // Create DTO and execute
        \$dto = {$studlyName}DTO::fromArray(\$transformedData);
        
        return \$this->execute(\$id, \$dto);
    }

    /**
     * Custom validation rules specific to this action
     */
    private function validateCustomRules(int \$id, array \$data): void
    {
        // Add custom validation logic here
        // Throw exceptions if validation fails
    }

    /**
     * Transform data before processing
     */
    private function transformData(array \$data): array
    {
        // Add any data transformation logic here
        return \$data;
    }
}
";
    }
}