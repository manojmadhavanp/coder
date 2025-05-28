<?php
namespace System\Core;

use System\Database\DBServer;
use System\Core\Logger;

class ModuleSetup {
    private $db;
    private $moduleId;
    private $modulePath;
    private $moduleConfig;

    public function __construct(int $moduleId, string $modulePath) {
        $this->db = DBServer::connect();
        $this->moduleId = $moduleId;
        $this->modulePath = $modulePath;
    }

    public function install(): bool {
        try {
            // Load module configuration
            $this->moduleConfig = $this->loadModuleConfig();
            if (!$this->moduleConfig) {
                return false;
            }

            // Check dependencies
            if (!$this->checkDependencies()) {
                return false;
            }

            // Install database tables
            if (!$this->installDatabase()) {
                return false;
            }

            // Install permissions
            if (!$this->installPermissions()) {
                return false;
            }

            // Install menu items
            if (!$this->installMenuItems()) {
                return false;
            }

            // Install routes
            if (!$this->installRoutes()) {
                return false;
            }

            // Copy assets
            if (!$this->installAssets()) {
                return false;
            }

            // Record installation
            return $this->recordInstallation();

        } catch (\Exception $e) {
            Logger::Error("Module installation failed: " . $e->getMessage());
            return false;
        }
    }

    private function loadModuleConfig(): ?array {
        $configFile = $this->modulePath . '/setup/module.json';
        if (!file_exists($configFile)) {
            Logger::Error("Module configuration not found: $configFile");
            return null;
        }

        $config = json_decode(file_get_contents($configFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Logger::Error("Invalid module configuration: " . json_last_error_msg());
            return null;
        }

        return $config;
    }

    private function checkDependencies(): bool {
        $dependencies = $this->moduleConfig['dependencies'] ?? [];

        // Check PHP extensions
        foreach ($dependencies['php_extensions'] ?? [] as $extension) {
            if (!extension_loaded($extension)) {
                Logger::Error("Required PHP extension not loaded: $extension");
                return false;
            }
        }

        // Check module dependencies
        foreach ($dependencies['modules'] ?? [] as $module) {
            if (!$this->isModuleInstalled($module)) {
                Logger::Error("Required module not installed: $module");
                return false;
            }
        }

        return true;
    }

    private function installDatabase(): bool {
        $sqlFiles = $this->moduleConfig['install']['sql_files'] ?? [];
        foreach ($sqlFiles as $file) {
            $setupConfig = $this->loadDatabaseConfig($file);
            if (!$setupConfig) continue;

            foreach ($setupConfig['tables'] as $table) {
                if (!$this->createTable($table)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function installPermissions(): bool {
        $permissions = $this->moduleConfig['permissions'] ?? [];
        $permissionCollection = $this->db->getCollection('permissions');

        foreach ($permissions as $permission) {
            try {
                $permissionCollection->Insert([
                    'module_id' => $this->moduleId,
                    'name' => $permission['name'],
                    'description' => $permission['description']
                ]);
            } catch (\Exception $e) {
                Logger::Error("Failed to install permission: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    private function installMenuItems(): bool {
        $menuItems = $this->moduleConfig['menu_items'] ?? [];
        $menuCollection = $this->db->getCollection('app_menus');

        foreach ($menuItems as $item) {
            try {
                $menuCollection->Insert([
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'icon' => $item['icon'],
                    'parent_id' => $item['parent'],
                    'order' => $item['order'],
                    'is_active' => true
                ]);
            } catch (\Exception $e) {
                Logger::Error("Failed to install menu item: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    private function installRoutes(): bool {
        $routes = $this->moduleConfig['routes'] ?? [];
        $routeCollection = $this->db->getCollection('routes');

        foreach ($routes as $route) {
            try {
                $routeCollection->Insert([
                    'module_id' => $this->moduleId,
                    'method' => $route['method'],
                    'path' => $route['path'],
                    'handler' => $route['handler'],
                    'is_protected' => $route['is_protected'] ?? false,
                    'roles' => json_encode($route['roles'] ?? [])
                ]);
            } catch (\Exception $e) {
                Logger::Error("Failed to install route: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    private function installAssets(): bool {
        $assets = $this->moduleConfig['install']['assets'] ?? [];
        $publicPath = BASE_PATH . 'public/';

        foreach ($assets as $type => $files) {
            foreach ($files as $file) {
                $sourcePath = $this->modulePath . '/assets/' . $file;
                $destPath = $publicPath . $file;

                if (!$this->copyAsset($sourcePath, $destPath)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function copyAsset(string $source, string $dest): bool {
        try {
            if (is_file($source)) {
                // Create destination directory if it doesn't exist
                $destDir = dirname($dest);
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                return copy($source, $dest);
            }
            return false;
        } catch (\Exception $e) {
            Logger::Error("Failed to copy asset: " . $e->getMessage());
            return false;
        }
    }

    private function recordInstallation(): bool {
        return $this->db->getCollection('modules')->Update(
            [
                'installed_version' => $this->moduleConfig['version'],
                'installed_at' => date('Y-m-d H:i:s'),
                'config' => json_encode($this->moduleConfig)
            ],
            ['module_id' => $this->moduleId]
        );
    }

    private function loadDatabaseConfig(string $filename): ?array {
        $configFile = $this->modulePath . '/setup/' . $filename;
        if (!file_exists($configFile)) {
            Logger::Error("Database configuration file not found: $configFile");
            return null;
        }

        try {
            $config = json_decode(file_get_contents($configFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Logger::Error("Invalid database configuration JSON: " . json_last_error_msg());
                return null;
            }

            // Validate required structure
            if (!isset($config['tables']) || !is_array($config['tables'])) {
                Logger::Error("Invalid database configuration structure: missing or invalid 'tables' section");
                return null;
            }

            return $config;

        } catch (\Exception $e) {
            Logger::Error("Failed to load database configuration: " . $e->getMessage());
            return null;
        }
    }

    private function isModuleInstalled(string $moduleSlug): bool {
        try {
            $module = $this->db->getCollection('modules')
                ->FindOne([
                    'slug' => $moduleSlug,
                    'installed_version IS NOT NULL'
                ]);
            return !empty($module);
        } catch (\Exception $e) {
            Logger::Error("Failed to check module installation status: " . $e->getMessage());
            return false;
        }
    }

    private function createTable(array $tableConfig): bool {
        try {
            // Create table
            $createSql = implode("\n", $tableConfig['sql']['create']);
            $this->db->exec($createSql);

            // Seed data if provided
            if (isset($tableConfig['sql']['seed'])) {
                $seedSql = implode("\n", $tableConfig['sql']['seed']);
                $this->db->exec($seedSql);
            }

            Logger::Info("Created table: {$tableConfig['name']}");
            return true;

        } catch (\Exception $e) {
            Logger::Error("Failed to create table {$tableConfig['name']}: " . $e->getMessage());
            return false;
        }
    }
} 