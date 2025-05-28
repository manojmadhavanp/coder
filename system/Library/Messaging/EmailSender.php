<?php

namespace System\Library\Messaging;

use System\Core\Logger;
use System\Library\Messaging\SenderInterface;

class EmailSender implements SenderInterface
{
    public function send(string $template, array $data, $recipients)
    {
        Logger::Info("EmailSender::send() called." );
        // Implement email sending logic here
    }
}