<?php


namespace Luur\Restponder;


use Closure;

trait HasCustomHandlers
{
    protected static $customHandlers = [];

    public static function registerHandler(string $className, Closure $handler): void
    {
        self::$customHandlers[$className] = $handler;
    }

    public static function resetHandlers(): void
    {
        self::$customHandlers = [];
    }

    protected function handleCustom(object $object): void
    {
        $handler = self::$customHandlers[get_class($object)];
        $handler($object, $this);
    }
}
