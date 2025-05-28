<?php

namespace System\Core;

class EventManager {
    private static $instance = null;
    private $listeners = [];

    // Private constructor to prevent instantiation
    private function __construct() {
        $this->listeners = []; // Initialize listeners in the constructor
    }

    // Method to add an event listener
    public static function addEventListener($event, $callback) {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        if (!isset(self::$instance->listeners[$event])) {
            self::$instance->listeners[$event] = [];
        }
        self::$instance->listeners[$event][] = $callback;
    }

    // Method to trigger an event
    public static function triggerEvent($event, $data = null) {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        if (isset(self::$instance->listeners[$event])) {
            foreach (self::$instance->listeners[$event] as $callback) {
                call_user_func($callback, $data);
            }
        }
    }
}
?>