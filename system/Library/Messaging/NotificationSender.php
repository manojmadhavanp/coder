<?php

namespace System\Library\Messaging;

use System\Core\Logger;
use System\Library\Messaging\SenderInterface;

class NotificationSender implements SenderInterface
{
    public function send(string $template, array $data, $recipients)
    {
        Logger::Info("NotifucationSender::send() called. ");
        // Implement email sending logic here
    }
}