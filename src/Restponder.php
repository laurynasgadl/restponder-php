<?php


namespace Luur\Restponder;


use Closure;

class Restponder
{
    public static function response($response): Response
    {
        return new Response($response);
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
        Response::registerHandler($className, $handler);
    }

    public static function registerErrorHandler(string $className, Closure $handler)
    {
        ErrorData::registerHandler($className, $handler);
    }

    public static function reset(): void
    {
        Response::resetHandlers();
        ErrorData::resetOptions();
    }
}
