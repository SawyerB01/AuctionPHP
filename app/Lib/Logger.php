<?php
namespace App\Lib;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;

/**
 * Class Logger
 * @package app\Lib
 */
class Logger
{
    /** @var Logger|null */
    private static $logger = null;

    /** @var Monolog|null */
    private $log = null;

    /**
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        if (!self::$logger) {
            self::$logger = new self();
        }
        return self::$logger;
    }
    public function __construct()
    {
        try {
            $channels = [
                new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Monolog::DEBUG),
                new StreamHandler(LOG_LOCATION, Monolog::DEBUG),
                new NativeMailerHandler(CONFIG_ADMINEMAL, "Critical Error", CONFIG_ADMINEMAL, Monolog::ALERT),
            ];

            $this->log = new Monolog('Auction');

            foreach ($channels as $channel) {
                $this->log->pushHandler($channel);
            }
        } catch (\Exception $e) {
            error_log("Critical Failure");
            die();
        }
    }

    public function emergency($message, array $context = []): void
    {
        $this->writeLog(Monolog::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->writeLog(Monolog::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->writeLog(Monolog::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->writeLog(Monolog::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->writeLog(Monolog::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->writeLog(Monolog::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->writeLog(Monolog::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->writeLog(Monolog::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->writeLog($level, $message, $context);
    }

    public function write($message, array $context = []): void
    {
        $this->writeLog(Monolog::DEBUG, $message, $context);
    }

    protected function writeLog($level, $message, array $context = []): void
    {
        $message = $this->formatMessage($message);
        $this->log->log($level, $message, $context);
    }

    protected function formatMessage($message): string
    {
        if (is_array($message) || is_object($message)) {
            return print_r($message, true);
        }
        return (string) $message;
    }
}