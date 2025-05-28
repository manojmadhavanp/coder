<?php

namespace System\Library;

use System\Core\Logger;
use System\Library\Messaging\EmailSender;
use System\Library\Messaging\SMSSender;
use System\Library\Messaging\NotificationSender;

class Sender
{
    public static function Create($type){
        switch ($type) {
            case 'email':
                return new EmailSender();
                break;
            case 'sms':
                return new SMSSender();
                break;
            case 'notification':
                return new NotificationSender();
                break;
            default:
                Logger::Error("Invalid sender type: {$type}");
                return null;
        }
    }
}