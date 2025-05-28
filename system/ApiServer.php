<?php


namespace System;

//define("BASE_PATH", str_replace('\\', '/', dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR);
/*define("BASE_PATH", dirname(__DIR__, 1) . DIRECTORY_SEPARATOR);
define("APP_PATH", BASE_PATH ."app". DIRECTORY_SEPARATOR);
define("APP_URI",  "./../app" . DIRECTORY_SEPARATOR);
define("VIEW_PATH", APP_PATH. 'views' . DIRECTORY_SEPARATOR);
define("TEMPLATE_PATH", VIEW_PATH .'templates' . DIRECTORY_SEPARATOR);
define("LOGFOLDER", BASE_PATH . "logs" . DIRECTORY_SEPARATOR);*/

if (!defined('BASE_PATH')) {
    define("BASE_PATH", dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!defined('LOGFOLDER')) {
    define("LOGFOLDER", BASE_PATH . "logs" . DIRECTORY_SEPARATOR);
}

if (!defined('API_PATH')){
    define('API_PATH', BASE_PATH . "api". DIRECTORY_SEPARATOR);
}
if (!defined('ROUTE_BASE')) {
    define("ROUTE_BASE", str_replace('\\', '/', API_PATH));
}

/*echo isset($_ENV) ? "env set" : "env not set";
echo empty($_ENV) ? "is empty" : "is not empty";

    $_ENV = array();*/


use System\HTTP\Request;
use System\HTTP\Response;
use System\Core\Logger;
use System\HTTP\Method;
use System\Core\Enverionment;
use System\Core\Auth;
use System\MVC\CollectionController;


class ApiServer
{

    static $instance = null;

    private array $config;
    private array $routes;
    private string $serverpath;
    private $currentGroupPrefix = '';
    private $currentModule = '';
    //private Collections $collections;
    //private array $caches;
    

    private function __construct()
    {
        self::Init();
        
    }


    public function Init()
    {
      //  $requestPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->routes = array();
        // $this->collectionController = CollectionController::getInstance();


    }

    public static function Start()
    {
        if (self::$instance === null) {
            self::$instance = new ApiServer();
            self::$instance->LoadEnv();

        }
        Logger::Info("API Server Started");
       
        // self::$instance->Routes($approutes);
        return self::$instance;

        // self::$instance->Process();
         }
 

    private function LoadEnv(){
        $envFilepath = API_PATH.".env";
       
        if (is_file($envFilepath)) {
            $file = new \SplFileObject($envFilepath);
            while (false === $file->eof()) {
               $fileline = trim($file->fgets());
              
                if(substr($fileline,0,1) != "#" && $fileline != ""){
                    $linecontent = explode("=",$fileline);
                    $_ENV[trim($linecontent[0])] = trim($linecontent[1]);
                }
            }
        }
        
    }
   
    public function RegisterCollections(array $collections)
    {
        foreach ($collections as $moduleName => $classNames) {
            foreach ($classNames as $className) {
                $fullClassName = "API\\modules\\$moduleName\\$className";
                $tablename = $className;
                    CollectionController::register($className, $fullClassName, $tablename);
                    Logger::Info("Registered collection: $className in module $moduleName and has tablename $tablename");
                
            }
        }
    }

    public function getAllCollections()
    {
        return CollectionController::getAll();
    }

    public function getRoutes()
    {
        return $this->routes;
    }
   
    public function Output($label, $handler)
    {
        echo "<br /><pre>$label:";
        print_r($handler);
        echo "</pre><br />";
    }

   

    public function process()
    {
        Logger::Info("Processing Route: $method $uri");
        $request = new Request();
        $response = new Response();
        $uriprotected = false;
       
        $uri = $request->GetURI();
        $method = $request->GetMethod();
        $isAuthurl = $request->isAuth();
        Logger::Debug("Processing Route: ". $uri);
        if($isAuthurl){
            $this->ManageAuth($request, $response);
            return;
        }
        if(!isset($this->routes[$method][$uri])){
            $this->MethodNotAllowed($uri, $method);
            die; 
        }
        
       
            $route = $this->routes[$method][$uri];
            $routehandler = isset($route['handler']) ? $route['handler'] : $route;
            Logger::Info("Processing Route: ". $uri);
            Logger::Debug($routehandler);
            Logger::Debug($route['protected']? "Protected" : "Not Protected");
            if(!$route['protected'])
            {
                Logger::Info("Processing Open Route: ". $uri);
            $this->handleRequest($routehandler, $request, $response);
            return;
           // $response::send();
            }
            
            Logger::Info("Processing Protected Route: ". $uri);
        $token = $request->getBearerToken();
       
        if(is_null($token) || strlen($token) == 0){
            Response::sendUnauthorized();
            die;
        }
        $Auth = new Auth();
        $userdata = null;
        if($Auth->isValidToken($token)){
            Logger::Info("Token Valid");
            $userdata = $Auth->GetPayload();
            Logger::Debug($userdata);
             $this->handleRequest($routehandler, $request, $response);
            return;
        }
        else{
            Logger::Info("Token Invalid");
            Response::sendUnauthorized();
            die;
        }

            
       
       
        $this->Output("RouteHandler", $routehandler);
        $this->Output("Request", $request);
        $this->Output("Response", $response);
    }

    private function ManageAuth($request, $response){
       Logger::Debug("Managing Auth");
        $Auth = new Auth();
       

        
        $this->Output("Request", $request);
        $this->Output("Response", $response);   
    }

    private function MethodNotAllowed($uri, $message)
    {
        Logger::Error("Method Not Allowed: $uri");
        $AllowedMethods = '';
        $response = new Response();
        $AllowedMethods .= isset($this->routes['Get'][$uri]) ? "GET, " : '';
        $AllowedMethods .= isset($this->routes['Post'][$uri]) ? "POST, " : '';
        $AllowedMethods .= isset($this->routes['Update'][$uri]) ? "PUT / PATCH, " : '';
        $AllowedMethods .= isset($this->routes['Delete'][$uri]) ? "DELETE, " : '';
        Response::sendMethodNotAllowed($AllowedMethods);
        $response->Send(405, 'Method Not Allowed');
      
    }
    private function handleRequest($handler, $req, $res)
    {
        Logger::Debug("Attempting to handle request with: " . $handler);

        $handlerParts = explode("::", $handler);
        if (count($handlerParts) !== 2) {
            Logger::Error("Invalid handler format: " . $handler);
            return;
        }

        $className = $handlerParts[0];
        $methodName = $handlerParts[1];

        Logger::Debug("Trying to load class: " . $className);
        Logger::Debug("Trying to load method: " . $methodName);

        if (!class_exists($className)) {
            Logger::Error("Class not found: " . $className);
            return;
        }

        Logger::Debug("Class found: " . $className);
        $classInstance = new $className();

        if (!method_exists($classInstance, $methodName)) {
            Logger::Error("Method not found: $className::$methodName");
            return;
        }

        Logger::Debug("Method found: " . $methodName);
        $response = call_user_func_array([$classInstance, $methodName], array($req, $res));
        
        // Handle the response...
    }
    private function Routes(array $approutes)
    {
        $this->routes = $approutes;
    }

   
    public function load()
    {
        return self::$instance;
    }

    

    public function RouteGroup($prefix, $module, $callback)
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousModule = $this->currentModule;
        $this->currentGroupPrefix .= $prefix;
        $this->currentModule = $module;
        
        $callback($this);
        
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentModule = $previousModule;
    }

    public function AddRoute($protected, $method, $path, $handler)
    {
        $fullPath = $this->currentGroupPrefix . $path;
        $handlerParts = explode('::', $handler);
        if (count($handlerParts) === 2) {
            $className = $handlerParts[0];
            $methodName = $handlerParts[1];
            if (strpos($className, '\\') === false) {
                $fullHandler = "Modules\\" . $this->currentModule . "\\" . $handler;
            } else {
                $fullHandler = "Modules\\" . $handler;
            }
        } else {
            $fullHandler = "Modules\\" . $this->currentModule . "\\" . $handler;
        }
        $fullHandler = str_replace('\\\\', '\\', $fullHandler);
        Logger::Info("Adding Route: $method $fullPath -> $fullHandler");
        $this->routes[$method][$fullPath] = ['handler' => $fullHandler, 'protected' => $protected];
    }

   
    public function Route($is_secured, $method, $path, $handler, $cache = false)
    {
        $path = strtolower($path);
        $this->routes[$method][$path] = array('path' => $path, 'handler' => $handler,'protected' => $is_secured); //$handler;
    }

};
