<?php

namespace System\Core;

use System\Core\Encryptor;

class Auth{
    private $header;
    private $secretKey;
    private $payload;
    private $auth_routes;
    private $encryptionMethod;
    private $secretIv;
    private $edsecretKey;

    function __construct()
    {
        $this->encryptionMethod = "AES-256-CBC";
        $this->header = ["alg" => "HS256", "typ" => "JWT"];
        $this->secretKey = self::base64url_encode($_ENV['AUTH_SECRET']);
        $this->edsecretKey = hash('sha256', $_ENV['AUTH_SECRET']);
        $this->secretIv = substr(hash('sha256', $_ENV['AUTH_SECRET']), 0, 16);
        $this->payload = NULL;
        $this->initAuthRoutes();
    }

    public function initAuthRoutes()
    {
        $this->auth_routes = array();
        $this->auth_routes['POST'][] = array("path" => "/auth/login", "handler" => "ValidateUser");
        $this->auth_routes['GET'][] = array("path" => "/auth/logout", "handler" => "LogoutUser");
        $this->auth_routes['POST'][] = array("path" => "/auth/reset-password", "handler" => "ResetUserPassword");
        $this->auth_routes['POST'][] = array("path" => "/auth/forgot-password", "handler" => "GetResetToken");
        $this->auth_routes['POST'][] = array("path" => "/auth/refresh", "handler" => "RefreshUserToken");
    }
    public function GenerateToken($payload)
    {
        if ($payload == NULL)
        return "";

        $payload['ValidFrom'] = time();
        $payload['ValidTill'] = time() + (60 * 60 * 24 * 7);
        $encoded_header = self::base64url_encode(json_encode($this->header));

        $encoded_payload = self::base64url_encode(json_encode($payload));
        $header_and_payload_combined = $encoded_header . '.' . $encoded_payload;
        $signature = self::base64url_encode(hash_hmac('sha256', $header_and_payload_combined, $this->secretKey, true));
        $jwt_token = $header_and_payload_combined . '.' . $signature;
        return self::base64url_encode($jwt_token);
    }

    public function isValidToken($token)
    {
        
        $token = self::base64url_decode($token);
        $jwt_values = explode('.', $token);

        $recieved_signature = $jwt_values[2];
        $recieved_header_and_payload = $jwt_values[0] . '.' . $jwt_values[1];
        $what_signature_should_be = self::base64url_encode(hash_hmac('sha256', $recieved_header_and_payload, $this->secretKey, true));
        $payload = json_decode(self::base64url_decode($jwt_values[1]), true);
        if ($what_signature_should_be == $recieved_signature) {

            if ((time() >= $payload['ValidFrom']) and (time() <= $payload['ValidTill'])) {
                unset($payload['ValidFrom']);
                unset($payload['ValidTill']);
                $this->payload = $payload;
                return true;
            } else
            return false;
        } else
        return false;
    }

    private static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    function GetPayload()
    {
        return $this->payload;
    }
    function SetPayload($userdata)
    {
        $this->payload = $userdata;
    }
    public function encrypt($data)
    {
        $output = openssl_encrypt($data, $this->encryptionMethod, $this->secretKey, 0, $this->secretIv);
        return base64_encode($output);
    }

    public function decrypt($data)
    {
        $output = openssl_decrypt(base64_decode($data), $this->encryptionMethod, $this->secretKey, 0, $this->secretIv);
        return $output;
    }
   

   
}
?>