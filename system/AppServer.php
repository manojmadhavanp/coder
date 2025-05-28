<?php

namespace System;

if (!defined('BASE_PATH')) {
    define("BASE_PATH", dirname(__DIR__, 1) . DIRECTORY_SEPARATOR);
}

if (!defined('LOGFOLDER')) {
    define("LOGFOLDER", BASE_PATH . "logs" . DIRECTORY_SEPARATOR);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . "app" . DIRECTORY_SEPARATOR);
}

if (!defined('MODULE_PATH')) {
    define('MODULE_PATH', BASE_PATH . "modules" . DIRECTORY_SEPARATOR);
}   

if (!defined('ROUTE_BASE')) {
    define("ROUTE_BASE", str_replace('\\', '/', APP_PATH));
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', APP_PATH . "views" . DIRECTORY_SEPARATOR);
}

if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', APP_PATH . "templates" . DIRECTORY_SEPARATOR);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

use System\HTTP\Request;
use System\HTTP\Response;
use System\Core\Logger;
use System\HTTP\Method;
use System\Core\Environment;
use System\Core\SessionManager;
use System\Core\EventManager;
use System\Database\DBServer;
use System\Config\SystemRoutes;

enum ServerType {
    case APP;
    case API;
    case STATIC;
}

class AppServer {

    static $instance = null;

    private array $config;
    private array $routes;
    private array $collections;
    private array $modules;
    private $db;

    private function __construct() {
        $this->Init();  
    }

    public function Init() {
        Logger::Info("Initializing AppServer");
       // $this->LoadEnv();
       // $this->db = DBServer::connect();
        $this->routes = array();
        $this->collections = array();
        $this->modules = array();
    }

    public static function Start(bool $debug = false) {
        Logger::Info("Starting AppServer");
        if (self::$instance === null) {
            self::$instance = new AppServer();
            Logger::DebugMode($debug);
           // self::$instance->LoadEnv();
            Logger::Info("AppServer instance created and environment loaded");

            // Trigger app started event
            EventManager::addEventListener('app.started', function() {
                Logger::Info("Application has started successfully.");
            });
            EventManager::triggerEvent('app.started');
        }
        Logger::Info("Server Started");
        return self::$instance;
    }

    public function AddCollections(array $appcollections) {
        $this->collections = array_merge($this->collections, $appcollections);
    }

    public function AddModules(array $appmodules) {
        $this->modules = array_merge($this->modules, $appmodules);
    }

    public function AddRoutes(array $approutes) {
        $this->routes = array_merge($this->routes, $approutes);
    }

    public function Route($method, $path, $handler, $cache = false) {
        $this->routes[$method][$path] = array('handler' => $handler, 'cache' => $cache, 'protected' => false);
    }

    public function SecureRoute($method, $path, $handler, $cache = false) {
        $this->routes[$method][$path] = array('handler' => $handler, 'cache' => $cache, 'protected' => true);
    }

    public function Process() {
        SessionManager::start();

        $request = new Request();
        $response = new Response();

        $uri = $request->GetURI();
        $method = $request->GetMethod();
        Logger::Info("Processing Route: $method $uri");

        $routeMatch = $this->matchRoute($method, $uri);
       
        if (!$routeMatch) {
            Logger::Error("No route found for: $method $uri");
            return $response->notFound();
        }

        $handler = $routeMatch['handler'];
        $params = $routeMatch['params'];

        // Add route parameters to the request object
        foreach ($params as $key => $value) {
            $request->addParam($key, $value);
        }

        if (isset($handler['protected']) && $handler['protected'] === true) {
            if (!SessionManager::isLoggedIn()) {
                Logger::Warning("Unauthorized access attempt to protected route: $method $uri");
                SessionManager::set('redirect_after_login', $uri);
                return $response->redirect('/login');
            } else {
                $userDetails = SessionManager::getUserDetails();
                $request->addParam('user', $userDetails);
            }
        }

        return $this->executeHandler($handler, $request, $response);
    }

    private function matchRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            Logger::Debug("No routes defined for method: $method");
            return null;
        }

        // Normalize URI (remove trailing slash unless it's just "/")
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes[$method] as $route => $handler) {
            $originalRoute = $route;

            // Normalize route (remove trailing slash unless it's just "/")
            if ($route !== '/' && substr($route, -1) === '/') {
                $route = rtrim($route, '/');
            }

            // Build regex pattern from route definition
            $pattern = preg_replace_callback('/\{\:([a-zA-Z0-9_]+)\}/', function ($matches) {
                return '(?P<' . $matches[1] . '>[^/]+)';
            }, $route);

            $pattern = '#^' . $pattern . '/?$#';

            Logger::Debug("Trying to match URI: $uri with pattern: $pattern");

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameters only
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                Logger::Info("Matched route: $originalRoute with params: " . json_encode($params));
                return [
                    'handler' => $handler['handler'],
                    'params' => $params
                ];
            }
        }

        Logger::Debug("No matching route found for: $method $uri");
        return null;
    }

    private function matchRouteOld2($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            Logger::Debug("No routes defined for method: $method");
            return null;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            // Normalize route
            $normalizedRoute = rtrim($route, '/');

            // Convert route parameters like {:param} to regex capture groups
            $pattern = preg_replace('/\{\:([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $normalizedRoute);
            $pattern = '#^' . $pattern . '/?$#';

            Logger::Debug("Matching URI: $uri against pattern: $pattern");

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Extract parameter names from route
                preg_match_all('/\{\:([a-zA-Z_][a-zA-Z0-9_]*)\}/', $normalizedRoute, $paramNames);
                $params = [];

                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        Logger::Debug("No matching route found for: $method $uri");
        return null;
    }
    private function matchRouteOld($method, $uri) {
        if (!isset($this->routes[$method])) {
            Logger::Debug("No routes defined for method: $method");
            return null;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            // Remove trailing slash from route if it exists
            if (substr($route, -1) === "/") {
                $route = rtrim($route, '/');
            }

            // Convert route parameters to regex pattern
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '/?$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove the full match

                // Extract parameter names from the route
                preg_match_all('/\{([^}]+)\}/', $route, $paramNames);
                $params = [];

                // Associate matched values with parameter names
                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        Logger::Debug("No matching route found for: $method $uri");
        return null;
    }

    private function executeHandler($handler, $req, $res) {
        try {
            if (is_array($handler) && isset($handler['handler'])) {
                $handlerString = $handler['handler'];
                // Add extracted params to the request object
                if (isset($handler['params'])) {
                    foreach ($handler['params'] as $key => $value) {
                        $req->addParam($key, $value);
                    }
                }
            } else {
                $handlerString = $handler;
            }

            list($className, $methodName) = explode('::', $handlerString);
            
            Logger::Debug("Attempting to execute handler: $className::$methodName");
            
            if (!class_exists($className)) {
                Logger::Error("Class not found: $className");
                return $res->serverError("Handler class not found");
            }

            $classInstance = new $className();
            
            if (!method_exists($classInstance, $methodName)) {
                Logger::Error("Method not found: $methodName in class $className");
                return $res->serverError("Handler method not found");
            }

            return call_user_func_array([$classInstance, $methodName], [$req, $res]);
        } catch (\Exception $e) {
            Logger::Error("Handler execution failed: " . $e->getMessage());
            return $res->serverError("Internal server error");
        }
    }

    public function load() {
        Logger::Debug("Loading AppServer instance");
        return self::$instance;
    }
   
    public function LoadDB() {
        $this->db = DBServer::connect();
    }
    public function LoadEnv($envFilepath) {
        Logger::Debug("Loading environment variables");

        if (is_file($envFilepath)) {
            $file = new \SplFileObject($envFilepath);
            while (false === $file->eof()) {
                $fileline = trim($file->fgets());

                if (substr($fileline, 0, 1) != "#" && $fileline != "") {
                    $linecontent = explode("=", $fileline, 2);  // Limit to 2 parts
                    if (count($linecontent) == 2) {
                        $key = trim($linecontent[0]);
                        $value = trim($linecontent[1]);
                        $_ENV[$key] = $value;
                        putenv("$key=$value");  // Also set as environment variable
                    }
                }
            }
            Logger::Info("Environment variables loaded successfully");
        } else {
            Logger::Warning("Environment file not found: $envFilepath");
        }
    }

    public function loadAppRoutes(int $appId) {
        try {
            if (!$this->db) {
                Logger::Error("Database connection not initialized");
                return;
            }

            $routes = $this->db->getCollection('routes')
                ->FilterBy(['app_id' => $appId]);

            foreach ($routes as $route) {
                // Validate required route properties
                if (!isset($route['method']) || !isset($route['path']) || !isset($route['handler'])) {
                    Logger::Warning("Invalid route configuration found");
                    continue;
                }

                $this->Route(
                    $route['method'],
                    $route['path'],
                    $route['handler'],
                    $route['is_protected'] ?? false
                );
                Logger::Debug("Loaded route: {$route['method']} {$route['path']}");
            }
        } catch (\Exception $e) {
            Logger::Error("Failed to load app routes: " . $e->getMessage());
        }
    }
}

Logger::Info("AppServer file loaded");

function base_url($path = '') {
    return BASE_URL . $path;
}