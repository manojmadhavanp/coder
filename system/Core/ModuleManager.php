<?php
namespace System\Core;

use System\Database\DBServer;
use System\Core\Logger;
use System\AppServer;

class ModuleManager {
    private static $instance = null;
    private $db;
    private $activeModules = [];
    private $appServer;

    private function __construct() {
        $this->db = DBServer::connect();
        $this->appServer = AppServer::Start();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadModules(int $appId): array {
        try {
            $modules = $this->db->getCollection('app_modules')
                ->FilterBy(['app_id' => $appId, 'is_active' => true]);

            foreach ($modules as $module) {
                $this->loadModule($module['module_id'], $module['config'] ?? null);
            }

            return $this->activeModules;
        } catch (\Exception $e) {
            Logger::Error("Failed to load modules: " . $e->getMessage());
            return [];
        }
    }

    private function loadModule(int $moduleId, ?array $config = null): void {
        $module = $this->db->getCollection('modules')
            ->FindOne(['module_id' => $moduleId]);

        if (!$module) {
            Logger::Error("Module not found: $moduleId");
            return;
        }

        // Check dependencies
        $dependencies = json_decode($module['dependencies'] ?? '[]', true);
        foreach ($dependencies as $dep) {
            if (!$this->isModuleActive($dep)) {
                Logger::Error("Missing dependency for module {$module['module_name']}: $dep");
                return;
            }
        }

        // Load module routes
        $this->loadModuleRoutes($moduleId);

        // Initialize module
        $moduleClass = "Modules\\{$module['module_slug']}\\LMSModule";
        if (class_exists($moduleClass)) {
            $moduleInstance = new $moduleClass($config);
            $this->activeModules[$module['module_slug']] = $moduleInstance;
        }
    }

    private function loadModuleRoutes(int $moduleId): void {
        try {
            $routes = $this->db->getCollection('routes')
                ->FilterBy(['module_id' => $moduleId]);

            foreach ($routes as $route) {
                $this->appServer->Route(
                    $route['method'],
                    $route['path'],
                    $route['handler'],
                    $route['is_protected'] ?? false
                );
                Logger::Debug("Loaded route: {$route['method']} {$route['path']}");
            }
        } catch (\Exception $e) {
            Logger::Error("Failed to load routes for module $moduleId: " . $e->getMessage());
        }
    }

    public function isModuleActive(string $moduleSlug): bool {
        return isset($this->activeModules[$moduleSlug]);
    }

    public function getModuleConfig(string $moduleSlug): ?array {
        return $this->activeModules[$moduleSlug]?->getConfig();
    }
} 