<?php

namespace System\Core;

class Encryptor
{
    private $encryptionMethod = 'AES-256-CBC';
    private $secretKey;
    private $secretIv;

    public function __construct()
    {
        $secretKey = $_ENV['AUTH_SECRET'];
        $secretIv = $_ENV['AUTH_SECRET'];
        $this->secretKey = hash('sha256', $secretKey);
        $this->secretIv = substr(hash('sha256', $secretIv), 0, 16);
    }

    public static function encrypt($data)
    {
        $output = openssl_encrypt($data, self::$encryptionMethod, self::$secretKey, 0, self::$secretIv);
        return base64_encode($output);
    }

    public static function decrypt($data)
    {
        $output = openssl_decrypt(base64_decode($data), self::$encryptionMethod, self::$secretKey, 0, self::$secretIv);
        return $output;
    }
}
?>