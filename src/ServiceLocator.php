<?php
/**
 * ServiceLocator, after Laravel's facade
 *
 * A service locator is responsible to locate a system-wide service.
 * With this class, you can define a service as a new class (like Laravel's facade).
 *
 * new class('Logger', $logger) extends ServiceLocator {};
 * \Logger::debug($msg);
 */

namespace tyam\radarx;

class ServiceLocator
{
    protected static $services;
    
    public function __construct($name, $service)
    {
        static::$services[get_called_class()] = $service;
        class_alias(get_called_class(), $name);
    }
    
    public static function __callStatic($name, $args)
    {
        $cls = get_called_class();
        if (! isset(static::$services[$cls])) {
            throw new \RuntimeException('service not found: ' . $cls);
        }
        $service = static::$services[$cls];
        return $service->$name(...$args);
    }
    
    public static function getInstance()
    {
        return static::$services[get_called_class()];
    }
}