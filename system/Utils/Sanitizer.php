<?php

namespace System\Utils;

class Sanitizer{

    public static function sanitize($input){  
        if (is_array($input)) {
            return self::sanitizeArray($input);
        } elseif (is_string($input)) {
            return self::sanitizeString($input);
        } else {
            return $input; // Return as-is for other types
        }
    }

    private static function sanitizeArray(array $input){
        foreach($input as $key => $value){
            $input[$key] = self::sanitize($value); // Recursive call for nested arrays
        }
        return $input;
    }

    private static function sanitizeString(string $value){
        return htmlspecialchars(stripslashes(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}
?>