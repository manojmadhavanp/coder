<?php

namespace Manage;

// Import Request and Response classes
use System\HTTP\Request;
use System\HTTP\Response;
use System\HTTP\Page;
use System\Core\SessionManager;
// Other use statements remain as they were
use System\Core\AuthOTP;
use System\Library\Sender;
use System\Messaging\Sender as MessagingSender;

define ("PAGE_PATH", __DIR__ .  DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR);
class Pages
{

    // Note: HomePage and LoginPage are not part of this refactoring task's scope, 
    // but would ideally be refactored similarly.
    function HomePage($req, $res) // Assuming $req, $res might be passed by router eventually
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->content(PAGE_PATH . 'home', array('csrfToken' => SessionManager::getCSRFToken()));
        // For full refactor, this would use $res->setContent($page->render(...))->send();
        echo $page->render('home.html'); 
    }

    function LoginPage($req, $res) // Assuming $req, $res might be passed by router eventually
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->content(PAGE_PATH . 'login', array('csrfToken' => SessionManager::getCSRFToken()));
        // For full refactor, this would use $res->setContent($page->render(...))->send();
        echo $page->render('login.html');
    }

    // Refactored DashboardPage
    public static function DashboardPage(Request $req, Response $res) 
    {
        self::requireLogin($req, $res); // Pass $req and $res

        $firstName = SessionManager::get('manage_user_first_name') ?? 'User';
        $page_title = 'Manager Dashboard';

        $page = new Page();
        $page->template("superadmin"); 
        $page->title($page_title);
        $page->description("Manager Dashboard Area");
        $dashboardTemplatePath = dirname(PAGE_PATH) . '/templates/dashboard.tpl';
        
        $page->content($dashboardTemplatePath, [
            'firstName' => $firstName,
            'page_title' => $page_title
        ]);
        
        $htmlContent = $page->render('dashboard.html');
        
        // Use Response object to send HTML content
        // Response class doesn't have a direct setContent() or setHtmlContent()
        // and then a send(). We set headers manually if needed and echo.
        // $res->json() also echoes and doesn't call exit, so we'll follow that pattern.
        header('Content-Type: text/html; charset=utf-8'); // Ensure correct content type
        echo $htmlContent;
        exit; // Ensure script termination
    }

    // TestPage remains unrefactored as it's not in scope
    function TestPage($req, $res)
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->template("test1");
        $page->content(PAGE_PATH . 'test', array());
        echo $page->render('test.html');
    }

    // Refactored handleLogin
    public static function handleLogin(Request $req, Response $res) {
        if ($req->getMethod() !== 'POST') {
            $res->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            exit;
        }

        // Request class constructor already handles JSON body parsing into params
        $identifier = $req->get('identifier');
        $password = $req->get('password');

        if (empty($identifier) || empty($password)) {
            $res->json(['success' => false, 'message' => 'Invalid input. Missing identifier or password.'], 400);
            exit;
        }

        // Get IP address and User Agent
        // TODO: Ideally, these should be available via methods on the Request object e.g., $req->getClientIp(), $req->getUserAgent()
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN_USER_AGENT';

        try {
            $dbServer = \System\Database\DBServer::connect(); 
            $authHandler = new \Manage\Auth\AuthHandler($dbServer);
            // Pass IP address and User Agent to the login method
            $result = $authHandler->login($identifier, $password, $ipAddress, $userAgent);

            if ($result['success']) {
                SessionManager::start(); // SessionManager is independent of Response object
                SessionManager::set('manage_user_uuid', $result['user_data']['uuid']);
                SessionManager::set('manage_user_first_name', $result['user_data']['first_name']);
                SessionManager::set('manage_user_last_name', $result['user_data']['last_name']);
                SessionManager::set('manage_user_role', $result['user_data']['userrole']);
                SessionManager::set('manage_authenticated', true);

                $res->json([
                    'success' => true,
                    'token' => $result['user_data']['uuid'], 
                    'redirect_url' => '/manage/dashboard' 
                ], 200);
            } else {
                $res->json(['success' => false, 'message' => $result['message']], 401);
            }
        } catch (\PDOException $e) {
            error_log("Database Error in handleLogin: " . $e->getMessage());
            $res->json(['success' => false, 'message' => 'A database error occurred.'], 500);
        } catch (\Exception $e) {
            error_log("Error in handleLogin: " . $e->getMessage());
            $res->json(['success' => false, 'message' => 'An internal error occurred.'], 500);
        }
        exit; // Ensure script termination as $res->json() does not exit.
    }

    // Refactored requireLogin
    public static function requireLogin(Request $req, Response $res) {
        SessionManager::start(); // SessionManager is independent of Response object

        if (
            SessionManager::get('manage_authenticated') !== true ||
            !SessionManager::get('manage_user_uuid')
        ) {
            SessionManager::set('manage_authenticated', false); 
            SessionManager::remove('manage_user_uuid');
            SessionManager::remove('manage_user_first_name');
            SessionManager::remove('manage_user_last_name');
            SessionManager::remove('manage_user_role');
            
            // Response::redirect already calls exit()
            $res->redirect('/manage/login'); 
        }
        // If we reach here, user is authenticated.
    }

    // Example usage (to be fully implemented in a future step if needed for actual page display)
    /*
    public static function showDashboardPage(Request $req, Response $res) { // Renamed to match existing naming convention like LoginPage
        self::requireLogin($req, $res); // Or Pages::requireLogin($req, $res);

        // Example of how you might retrieve data and display it
        $firstName = SessionManager::get('manage_user_first_name');
        
        // In a real scenario, you would use the Page class to render a template
        // $page = new Page();
        // $page->title("Manager Dashboard");
        // $page->content(PAGE_PATH . 'dashboard_content', ['firstName' => $firstName]);
        // $htmlContent = $page->render('dashboard.html'); 
        // header('Content-Type: text/html; charset=utf-8');
        // echo $htmlContent;
        // exit;
    }
    */
   
};
