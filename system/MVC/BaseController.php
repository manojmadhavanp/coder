<?php

namespace System\MVC;

use System\MVC\UserController;

class Permission
{
    const READ = 1;
    const WRITE = 2;
    const DELETE = 4;
}

abstract class BaseController extends UserController{

    public function __construct()
    {
        $this->initializeModel();
    }

    abstract protected function initializeModel();

    public static function NewUUID(): string{
        $data = random_bytes(16);

        // Set version to 0100
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        // Format the UUID as a string
        return vsprintf('%s-%s-%s-%s-%s', str_split(bin2hex($data), [8, 4, 4, 4, 12]));
    }
    
}

?>