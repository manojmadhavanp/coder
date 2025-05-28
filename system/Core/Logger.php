<?php

namespace System\Core;

date_default_timezone_set("Asia/Kolkata");
define("LOGPATH", LOGFOLDER . "/" . date('m-d-Y') . ".log");


class LogType
{
    const LOGERROR = 'ERROR';
    const LOGWARNING = 'WARNING';
    const LOGDEBUG = 'DEBUG';
    const LOGINFO = 'INFO';
    const LOGTIME = 'TIME';
    
}

class Logger
{
    static $instance = null;
    static $debugmode = false;
    var $Logfile = null;
    static $Filelog = null;
    var $starttime = null;
    private function __construct()
    {
            $this->Logfile = fopen(LOGPATH, 'a');
            self::$Filelog = $this->Logfile;
            if (self::$debugmode)    
            fwrite($this->Logfile, date('d-m-Y h:m:s') . ' ' . LogType::LOGINFO . ' ' . 'LOGGER STARTED' . PHP_EOL);
        
    }
    function __destructor()
    {
        fclose(self::$Logfile);
    }
    public function StartTime()
    {
        if (self::$debugmode)
        $this->starttime = microtime(true);

    }
    public function StopTime($Msg)
    {
        if (self::$debugmode){
            $starttime = $this->starttime;
            $timetaken = number_format((microtime(true) - $starttime), 4);
            $this->starttime = null;

            fwrite($this->Logfile, date('d-m-Y h:m:s') . ' ' . LogType::LOGTIME . ':' . $timetaken . ' Process:' . $Msg . PHP_EOL);
        }
    }
    public static function Log(LogType $logtype, $logmsg)
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        if (self::$debugmode)
            fwrite(self::$Filelog, date('d-m-Y h:m:s') . ' ' . $logtype . ' ' . $logmsg . PHP_EOL);
          
    }
    public static function Debug($logmsg)
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
       
        // Check if $logmsg is an array, object, or other type
        if (is_array($logmsg)) {
            // Print array in a readable format
            $formattedMsg = json_encode($logmsg, JSON_PRETTY_PRINT);
        } elseif (is_object($logmsg)) {
            // Print object properties
            $formattedMsg = print_r($logmsg, true);
        } else {
            // For other types, convert to string
            $formattedMsg = (string)$logmsg;
        }
        if (self::$debugmode)
        fwrite(self::$Filelog, date('d-m-Y h:m:s') . ' ' . LogType::LOGDEBUG . ' ' . $formattedMsg . PHP_EOL);
    }
    public static function Info($logmsg)
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        if (self::$debugmode)
        fwrite(self::$Filelog, date('d-m-Y h:m:s') . ' ' . LogType::LOGINFO . ' ' . $logmsg . PHP_EOL);
    }
    public static function Warning($logmsg)
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        if (self::$debugmode)
        fwrite(self::$Filelog, date('d-m-Y h:m:s') . ' ' . LogType::LOGWARNING . ' ' . $logmsg . PHP_EOL);
    }
    public static function Error($logmsg)
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        if(self::$debugmode)
        fwrite(self::$Filelog, date('d-m-Y h:m:s') . ' ' . LogType::LOGERROR . ' ' . $logmsg . PHP_EOL);
    }

    public static function DebugMode(bool $debugmode){
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        self::$debugmode = $debugmode;
    }
   



};
?>