<?php
namespace System\Core;

class SystemConfig {
    private static $instance = null;
    private $configPath;
    private $encryptionKey;
    private $config;

    private function __construct() {
        $this->configPath = dirname(__DIR__) . '/config/system.dat';
        $this->encryptionKey = getenv('APP_KEY') ?: 'default-encryption-key';
        $this->loadConfig();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig() {
        if (file_exists($this->configPath)) {
            $encrypted = file_get_contents($this->configPath);
            $decrypted = $this->decrypt($encrypted);
            $this->config = json_decode($decrypted, true) ?? [];
        } else {
            $this->config = [
                'apps' => [],
                'modules' => [
                    'auth' => [
                        'name' => 'Authentication',
                        'version' => '1.0.0',
                        'is_core' => true,
                        'status' => 'available'
                    ],
                    'cms' => [
                        'name' => 'Content Management',
                        'version' => '1.0.0',
                        'is_core' => true,
                        'status' => 'available'
                    ],
                    'lms' => [
                        'name' => 'Learning Management',
                        'version' => '1.0.0',
                        'is_core' => false,
                        'status' => 'available'
                    ]
                ]
            ];
        }
    }

    public function saveConfig() {
        $json = json_encode($this->config);
        $encrypted = $this->encrypt($json);
        return file_put_contents($this->configPath, $encrypted);
    }

    public function registerApp(array $appData) {
        $appId = uniqid('app_', true);
        $this->config['apps'][$appId] = array_merge($appData, [
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ]);
        return $this->saveConfig();
    }

    public function getApps(): array {
        return $this->config['apps'];
    }

    public function getModules(): array {
        return $this->config['modules'];
    }

    private function encrypt(string $data): string {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    private function decrypt(string $data): string {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
    }
} 