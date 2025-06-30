<?php

namespace App\Helpers;

class ModuleLoader
{
    private static array $loadedModules = [];
    
    public static function loadAllModules(): void
    {
        $modulesPath = app_path('Modules');
        $modules = array_diff(scandir($modulesPath), ['.', '..']);
        
        foreach ($modules as $module) {
            self::loadModule($module);
        }
    }
    
    public static function loadModule(string $moduleName): void
    {
        if (in_array($moduleName, self::$loadedModules)) {
            return;
        }
        
        $modulePath = app_path("Modules/{$moduleName}");
        
        if (!is_dir($modulePath)) {
            return;
        }
        
        // Panel routes
        $panelRoutesPath = $modulePath . '/Panel/routes.php';
        if (file_exists($panelRoutesPath)) {
            require $panelRoutesPath;
        }
        
        // Web routes
        $webRoutesPath = $modulePath . '/Web/routes.php';
        if (file_exists($webRoutesPath)) {
            require $webRoutesPath;
        }
        
        // Ana modül routes
        $moduleRoutesPath = $modulePath . '/routes.php';
        if (file_exists($moduleRoutesPath)) {
            require $moduleRoutesPath;
        }
        
        self::$loadedModules[] = $moduleName;
    }
    
    public static function getLoadedModules(): array
    {
        return self::$loadedModules;
    }
} 