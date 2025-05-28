<?php

namespace Manage;

use System\HTTP\Request;
use System\HTTP\Response;
use System\HTTP\Page;
use System\Core\SessionManager;
use System\Core\AuthOTP;
use System\Library\Sender;
use System\Messaging\Sender as MessagingSender;

define ("PAGE_PATH", __DIR__ .  DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR);
class Pages
{

    function HomePage($req, $res)
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->content(PAGE_PATH . 'home', array('csrfToken' => SessionManager::getCSRFToken()));
        echo $page->render('home.html');
    }

    function LoginPage($req, $res)
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->content(PAGE_PATH . 'login', array('csrfToken' => SessionManager::getCSRFToken()));
        echo $page->render('login.html');
    }

    function DashboardPage($req, $res)
    {
        $page = new Page();
        $page->template("superadmin");
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->content(PAGE_PATH . 'home', array('csrfToken' => SessionManager::getCSRFToken()));
        echo $page->render('dashboard.html');
    }

    function TestPage($req, $res)
    {
        $page = new Page();
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        $page->template("test1");
        $page->content(PAGE_PATH . 'test', array());
        echo $page->render('test.html');
    }
   
};
