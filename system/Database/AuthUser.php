<?php

namespace System\Database;

use System\Database\DBAuth;
use System\Core\Logger;

class AuthUser extends DBAuth
{
    private $db;
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "auth_users";
        $this->db = DBServer::connect();
    }

    public function CreateNewUser($id, $password)
    {
        try {
            return $this->db->getCollection($this->table)->Insert([
                'uid' => $id,
                'utoken' => password_hash($password, PASSWORD_DEFAULT),
                'last_login' => null
            ]);
        } catch (\Exception $e) {
            Logger::Error("Failed to create new user: " . $e->getMessage());
            return false;
        }
    }

    private function GetUsernameType($usernamestring)
    {
        $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $mobilePattern = '/^[1-9]\d{1,14}$/';

        if (preg_match($emailPattern, $usernamestring)) {
            return 'email';
        } elseif (preg_match($mobilePattern, $usernamestring)) {
            return 'mobile';
        } else {
            return 'username';
        }
    }

    public function ValidateUser($Username, $password)
    {
        try {
            $usernametype = $this->GetUsernameType($Username);
            
            // Get user profile first
            $userProfile = $this->db->getCollection('user_profiles')
                                  ->FindOne([$usernametype => $Username]);
            
            if (!$userProfile) {
                Logger::Debug("User not found with $usernametype: $Username");
                return null;
            }

            // Get auth details
            $authUser = $this->db->getCollection($this->table)
                                ->FindOne(['uid' => $userProfile['uid']]);
            
            if ($authUser && password_verify($password, $authUser['utoken'])) {
                // Update last login
                $this->db->getCollection($this->table)->Update(
                    ['last_login' => date('Y-m-d H:i:s')],
                    ['uid' => $userProfile['uid']]
                );
                
                // Return combined user data
                unset($authUser['utoken']); // Remove password hash
                return array_merge($userProfile, $authUser);
            }
            
            Logger::Debug("Invalid password for user: $Username");
            return null;

        } catch (\Exception $e) {
            Logger::Error("Authentication error: " . $e->getMessage());
            return null;
        }
    }
}