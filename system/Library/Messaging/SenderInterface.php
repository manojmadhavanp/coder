<?php

namespace System\Library\Messaging;


interface SenderInterface
{
    public function send(string $template, array $data, $recipients);
}