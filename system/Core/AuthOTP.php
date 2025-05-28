<?php

namespace System\Core;

use System\Core\SessionManager;


class AuthOTP
{
    public static function generate()
    {
        $characters = 6;

        $possible = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ#@$';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        SessionManager::set('secaccesscode', md5($code));
        //$_SESSION['secaccesscode'] = md5($code);
        return $code;
    }

    public static function check($code)
    {
        return md5($code) == SessionManager::get('secaccesscode');  
    }
}

