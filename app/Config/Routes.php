<?php

namespace App\Config;

use System\HTTP\Method;

class Routes{
    public function getRoutes(): array
    {
        return [
            Method::GET->value => [
               '/' => ['handler' => 'App\Pages::HomePage'],
                '/real-estate/{:for}/{:spacetype}-in-{:location}' => ['handler' => 'App\Pages::TestPage'],
                '/{:location}/{:category}' => ['handler' => 'App\Pages::TestPage'],
                '/{:listing}' => ['handler' => 'App\Pages::TestPage'],
            ],
            Method::POST->value => [
            ]    
        ];
    }
}
?>