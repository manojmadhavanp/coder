<?php

namespace System\MVC;

class UserController{

    public function get_profile(){

    }
    public function hasPermission($permission, $appid = NULL) {
        define("READ", 1);   // 001
        define("WRITE", 2);  // 010
        define("DELETE", 4); // 100
        
        // Check if the user's rights include the requested permission using bitwise AND
        return ($this->userRights & $permission) === $permission;
    }
    
}