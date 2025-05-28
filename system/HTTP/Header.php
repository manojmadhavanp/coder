<?php

namespace System\HTTP;

class Header{
    private array $headers;

    public function Params(string $key){
       return isset($this->headers[$key])? : null;
    }

    protected function Set(string $key, string $val){
        $this->headers[$key] = $val;
    }
};

?>
