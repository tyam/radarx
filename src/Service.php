<?php
/**
 * Service
 *
 * アプリケーションの全コードから参照可能な基本サービスをまとめたクラス。
 * ロギングとPubSub。
 *
 */

namespace tyam\radarx;

use Psr\Log\LoggerInterface;

class Service
{
    protected static $singleton;
    private $logger;
    private $dispatcher;

    public function __construct($logger, Callable $dispatcher) 
    {
        if (self::$singleton) {
            throw new \LogicException('singleton duplicated!');
        }
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        self::$singleton = $this;
    }

    public static function hasSingleton(): bool 
    {
        return (! is_null(self::$singleton));
    }

    public static function dispatch($event)
    {
        $d = self::$singleton->dispatcher;
        $d($event);
    }

    public static function debug($message, array $context = [])
    {
        self::$singleton->logger->debug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::$singleton->logger->info($message, $context);
    }
}
