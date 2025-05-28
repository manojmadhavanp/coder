<?php

namespace Manage;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Core\SessionManager;



define("PAGE_PATH", __DIR__ .  DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR);
class User
{
    function Varify($req, $res)
    {
        $retval = array('status' => false, 'message' => 'Login failed', 'redirect' => '');
        if(SessionManager::has('humancheckcode') and SessionManager::has('secaccesscode') and SessionManager::has('csrf_token')){
            
            if(SessionManager::get('humancheckcode') == md5($req->get('captcha')) and SessionManager::get('secaccesscode') == $req->get('otp') and SessionManager::varifyCSRFToken($req->get('csrf_token'))){
                $retval['status'] = true;
                $retval['message'] = 'You are logged in successfully';
                $retval['redirect'] = './dashboard';
                SessionManager::remove('humancheckcode');
                SessionManager::remove('secaccesscode');
                SessionManager::set('user_id', 'SUPERUSER');
                echo json_encode($retval);
                return true;
            }
            else{

                $retval['message'] = 'Wrong Credentials Received.';
                echo json_encode($retval);
                echo $req->get('captcha');
                echo $req->get('otp');
                echo $req->get('csrf_token');
                return false;
            }
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode($retval);
            return false;
        }
       
    }
};
