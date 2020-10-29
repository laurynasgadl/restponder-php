<?php


namespace Luur\Restponder;


use Closure;

class Restponder
{
    public static function content($response): ResponseContent
    {
        return new ResponseContent($response);
    }

    public static function setErrorDefaultCode(int $code): void
    {
        ErrorData::$defaultCode = $code;
    }

    public static function setErrorDefaultMessage(?string $message): void
    {
        ErrorData::$defaultMessage = $message;
    }

    public static function setErrorIncludeDebug(bool $includeDebug): void
    {
        ErrorData::$includeDebug = $includeDebug;
    }

    public static function registerResponseHandler(string $className, Closure $handler)
    {
        ResponseContent::registerHandler($className, $handler);
    }

    public static function registerErrorHandler(string $className, Closure $handler)
    {
        ErrorData::registerHandler($className, $handler);
    }

    public static function reset(): void
    {
        ResponseContent::resetHandlers();
        ErrorData::resetOptions();
    }
}
