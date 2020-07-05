<?php

namespace Spreng\system\log;

use Spreng\config\GlobalConfig;

/**
 * Logger
 */
class Logger
{
    public static function console_log($data)
    {
        echo '<script>';
        echo 'console.log(' . json_encode($data) . ')';
        echo '</script>';
    }

    public static function print(array $object, bool $print_r = false, bool $htmbr = false)
    {
        if (!$print_r) {
            if ($htmbr) $lb = "</br>";
            else $lb = "\n";
            foreach ($object as $key => $value) {
                if (is_array($value)) {
                    echo "<" . $key . ">" . $lb;
                    self::print($value, $print_r, $htmbr);
                    echo "</" . $key . ">" . $lb;
                } else {
                    echo $key . " = " . $value . $lb;
                }
            }
        } else {
            print_r($object);
        }
    }

    public static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    public static function logFile(string $type, string $data, array $context = [], int $backtrace = 1)
    {
        if (!GlobalConfig::getSystemConfig()->isLogActive()) return false;
        date_default_timezone_set('America/Sao_Paulo');
        $sysConfig = GlobalConfig::getSystemConfig();

        $logFile = $sysConfig->getSourcePath() . '/' . $sysConfig->getLogFile();

        if (!file_exists($logFile)) {
            $fp = fopen($logFile, "wb");
            fwrite($fp, '');
            fclose($fp);
        }
        //FileUtils::save($logFile);

        $log = new \Monolog\Logger(debug_backtrace()[$backtrace]['class'] . '\\' . debug_backtrace()[$backtrace]['function']);

        $log->pushHandler(new \Monolog\Handler\StreamHandler($logFile, \Monolog\Logger::DEBUG));
        $log->{$type}($data . '</br><hr>', $context);
    }

    public static function debug($msg, bool $break = false)
    {
        if ($break) {
            print_r($msg);
            exit;
        } else {
            self::logFile(__FUNCTION__, json_encode($msg, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE), [], 2);
        }
    }

    public static function info(string $msg)
    {
        self::logFile(__FUNCTION__, $msg, [], 2);
    }

    public static function alert(string $msg)
    {
        self::logFile(__FUNCTION__, $msg, [], 2);
    }

    public static function warning(string $msg)
    {
        self::logFile(__FUNCTION__, $msg, [], 2);
    }

    public static function error(string $msg)
    {
        self::logFile(__FUNCTION__, $msg, [], 2);
    }

    public static function getLogs(): string
    {
        $sysConfig = GlobalConfig::getSystemConfig();

        $logFile = $sysConfig->getSourcePath() . '/' . $sysConfig->getLogFile();
        return file_get_contents($logFile);
    }

    public static function clean()
    {
        $sysConfig = GlobalConfig::getSystemConfig();

        $logFile = $sysConfig->getSourcePath() . '/' . $sysConfig->getLogFile();
        $fh = fopen($logFile, 'w');
        fclose($fh);
    }
}
