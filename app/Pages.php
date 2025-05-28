<?php

namespace App;

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
        $page->template('webhome');
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        echo $page->render('index.html');
    }
    function TestPage($req, $res)
    {
        $page = new Page();
        $page->template('category');
        $page->title("Manage NMWeb");
        $page->description("Managing Navi Mumbai Web Site");
        echo $page->render('index.html');
       
    }

   
};
