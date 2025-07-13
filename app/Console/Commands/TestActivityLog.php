<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\ActivityLog\Models\Activity;

class TestActivityLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:test {model?} {--list : List all models with activity log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test activity log functionality for models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listModels();
            return;
        }

        $modelName = $this->argument('model');
        
        if (!$modelName) {
            $this->error('Model adı belirtilmedi. Kullanım: php artisan activity:test ModelName');
            $this->listModels();
            return;
        }

        $this->testModel($modelName);
    }

    /**
     * List all models with activity log
     */
    private function listModels()
    {
        $this->info('Activity Log Destekli Modeller:');
        $this->line('');
        
        $models = [
            'App\\Modules\\User\\Models\\User' => 'Kullanıcı',
            'App\\Modules\\User\\Models\\Role' => 'Rol',
            'App\\Modules\\User\\Models\\Permission' => 'Yetki',
            'App\\Modules\\Settings\\Models\\Setting' => 'Ayar',
            'App\\Modules\\MailNotification\\Models\\MailLog' => 'Mail Log',
        ];

        foreach ($models as $model => $displayName) {
            $this->line("  • {$displayName} ({$model})");
        }

        $this->line('');
        $this->info('Test için: php artisan activity:test User');
    }

    /**
     * Test specific model
     */
    private function testModel($modelName)
    {
        $modelMap = [
            'user' => 'App\\Modules\\User\\Models\\User',
            'role' => 'App\\Modules\\User\\Models\\Role',
            'permission' => 'App\\Modules\\User\\Models\\Permission',
            'setting' => 'App\\Modules\\Settings\\Models\\Setting',
            'maillog' => 'App\\Modules\\MailNotification\\Models\\MailLog',
        ];

        $modelClass = $modelMap[strtolower($modelName)] ?? $modelName;

        if (!class_exists($modelClass)) {
            $this->error("Model bulunamadı: {$modelClass}");
            return;
        }

        $this->info("Testing activity log for: {$modelClass}");
        $this->line('');

        try {
            // Get first record
            $model = $modelClass::first();
            
            if (!$model) {
                $this->warn("Model'de kayıt bulunamadı. Test için önce bir kayıt oluşturun.");
                return;
            }

            $this->info("Test edilen kayıt ID: {$model->id}");

            // Get current log count
            $beforeCount = Activity::count();
            $this->line("Log sayısı (önce): {$beforeCount}");

            // Update model
            $updateData = $this->getUpdateData($modelClass);
            $model->update($updateData);

            // Get new log count
            $afterCount = Activity::count();
            $this->line("Log sayısı (sonra): {$afterCount}");

            if ($afterCount > $beforeCount) {
                $this->info('✅ Activity log başarıyla oluşturuldu!');
                
                // Get latest log
                $latestLog = Activity::latest()->first();
                $this->line("Son log: {$latestLog->description}");
                $this->line("Event: {$latestLog->event}");
                $this->line("Subject: {$latestLog->subject_type}");
                
                if ($latestLog->changes) {
                    $this->line('Değişiklikler:');
                    foreach ($latestLog->changes as $field => $values) {
                        if (isset($values['old']) && isset($values['new'])) {
                            $this->line("  • {$field}: {$values['old']} → {$values['new']}");
                        } else {
                            $this->line("  • {$field}: " . json_encode($values));
                        }
                    }
                }
            } else {
                $this->error('❌ Activity log oluşturulamadı!');
            }

        } catch (\Exception $e) {
            $this->error("Hata: {$e->getMessage()}");
        }
    }

    /**
     * Get update data for specific model
     */
    private function getUpdateData($modelClass)
    {
        $timestamp = time();
        
        switch ($modelClass) {
            case 'App\\Modules\\User\\Models\\User':
                return ['first_name' => "Test User {$timestamp}"];
            
            case 'App\\Modules\\User\\Models\\Role':
                return ['description' => "Test Role {$timestamp}"];
            
            case 'App\\Modules\\User\\Models\\Permission':
                return ['description' => "Test Permission {$timestamp}"];
            
            case 'App\\Modules\\Settings\\Models\\Setting':
                return ['description' => "Test Setting {$timestamp}"];
            
            case 'App\\Modules\\MailNotification\\Models\\MailLog':
                return ['subject' => "Test Mail {$timestamp}"];
            
            default:
                return ['description' => "Test Update {$timestamp}"];
        }
    }
} 