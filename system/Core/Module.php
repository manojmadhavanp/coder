<?php
namespace System\Core;

abstract class Module {
    protected $config;

    public function __construct(?array $config = null) {
        $this->config = $config ?? [];
        $this->initialize();
    }

    abstract protected function initialize(): void;
    abstract public function getModuleInfo(): array;

    public function getConfig(): array {
        return $this->config;
    }
} 