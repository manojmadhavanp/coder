<?php

namespace System\Library\Messaging;

use System\Core\Logger;
use System\Library\Messaging\SenderInterface;

class SMSSender implements SenderInterface
{
    public function send(string $template, array $data, $recipients)
    {
        Logger::Info("SMSSender::send() called. ");
        // Implement email sending logic here
    }
}