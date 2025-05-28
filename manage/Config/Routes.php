<?php

namespace Manage\Config;

use System\HTTP\Method;

class Routes{
    public function getRoutes(): array
    {
        return [
            Method::GET->value => [
               '/' => ['handler' => 'Manage\Pages::HomePage'],
                '/login' => ['handler' => 'Manage\Pages::LoginPage'],
                '/dashboard' => ['handler' => 'Manage\Pages::DashboardPage', 'protected' => true],
               '/test' => ['handler' => 'Manage\Pages::TestPage'],
            ],
            Method::POST->value => [
                '/api/login' => ['handler' => 'Manage\User::Login'],
                '/api/verify' => ['handler' => 'Manage\User::Varify'],
                '/api/user/logout' => ['handler' => 'Manage\User::Logout'],
                '/api/login/send-token' => ['handler' => 'Manage\User::GetResetToken'],
                '/api/captcha/refresh' => ['handler' => 'Manage\User::ResetUserPassword'],
            ]    
        ];
    }
}
?>